<?php
namespace SpaceCode\Maia;

use Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use App\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use SpaceCode\Maia\Contracts\Role as RoleContract;
use SpaceCode\Maia\Contracts\Permission as PermissionContract;
use SpaceCode\Maia\Contracts\Page as PageContract;
use SpaceCode\Maia\Http\Middleware\FilemanagerAuthorize;
use SpaceCode\Maia\Http\Middleware\SettingsAuthorize;
use SpaceCode\Maia\Http\Middleware\SeoAuthorize;

class ToolServiceProvider extends ServiceProvider
{
    public function boot(PermissionRegistrar $permissionLoader, Filesystem $filesystem, User $user)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/filemanager', 'maia-filemanager');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/settings', 'maia-settings');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/seo', 'maia-seo');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'maia');
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/maia.php', 'maia');
        }

        $this->commands([
            Commands\CacheReset::class,
            Commands\CreateRole::class,
            Commands\CreatePermission::class,
            Commands\ShowPermission::class,
            Commands\PublishCommand::class,
        ]);

        $this->app->booted(function () {
            $this->routes();
        });
        $this->registerPolicies($user);
        Nova::serving(function (ServingNova $event) {
            Nova::script('maia-sluggable', __DIR__.'/../dist/js/sluggable.js');
            Nova::script('filemanager-field', __DIR__.'/../dist/js/filemanager-field.js');
            Nova::script('image-field', __DIR__.'/../dist/js/advanced-image.js');
            Nova::script('toggle', __DIR__.'/../dist/js/toggle.js');
            Nova::style('toggle', __DIR__.'/../dist/css/toggle.css');
        });
        $this->registerMacroHelpers();
        $this->registerModelBindings();
        $this->registerBladeExtensions();
        $this->loadHelper();
        $permissionLoader->registerPermissions();
        $this->app->singleton(PermissionRegistrar::class, function ($app) use ($permissionLoader) {
            return $permissionLoader;
        });
    }

    protected function loadHelper() {
        require_once __DIR__.'/helpers.php';
        if (file_exists(app_path('/Http/helpers.php'))) {
            require_once app_path('/Http/helpers.php');
        }
    }

    protected function registerPolicies($user)
    {
        Gate::before(function ($user) {
            foreach ($user->roles as $role) {
                return $role->name === 'developer' ? true : null;
            }
        });
        Gate::policy(config('maia.models.user'), Policy\UserPolicy::class);
        Gate::policy(config('maia.models.permission'), Policy\PermissionPolicy::class);
        Gate::policy(config('maia.models.role'), Policy\RolePolicy::class);
        Gate::policy(config('maia.models.page'), Policy\PagePolicy::class);
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/maia.php' => config_path('maia.php'),
        ], 'maia-config');

//        $this->publishes([
//            __DIR__.'/../public' => public_path('vendor/nova'),
//        ], 'nova-assets');

        $this->publishes([
            __DIR__.'/../lang' => resource_path('lang/vendor/maia'),
        ], 'maia-lang');

        $this->publishes([
            __DIR__.'/../resources/views/index' => resource_path('views'),
        ], 'nova-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'maia-migrations');

        $this->publishes([
            __DIR__.'/../database/seeds' => database_path('seeds'),
        ], 'maia-seeds');
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }
        \Illuminate\Support\Facades\Route::middleware(['nova'])
            ->prefix('nova-vendor/maia-sluggable')
            ->group(__DIR__.'/../routes/sluggable.php');

        \Illuminate\Support\Facades\Route::middleware(['nova', FilemanagerAuthorize::class])
            ->namespace('SpaceCode\Maia\Http\Controllers')
            ->prefix('nova-vendor/maia-filemanager/nova-filemanager')
            ->group(__DIR__.'/../routes/filemanager.php');

        \Illuminate\Support\Facades\Route::middleware(['nova', SettingsAuthorize::class])
            ->group(__DIR__ . '/../routes/settings.php');

        \Illuminate\Support\Facades\Route::middleware(['nova', SeoAuthorize::class])
            ->group(__DIR__ . '/../routes/seo.php');

        // Index
//        \Illuminate\Support\Facades\Route::get('/', ['uses' => 'VoxIndexController@home_show', 'as' => 'home']);
//        \Illuminate\Support\Facades\Route::get(setting('seo-pages.prefix') . '/{slug}', ['uses' => 'MaiaIndexController@pages_show', 'as' => 'page']);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function registerModelBindings()
    {
        $config = $this->app->config['maia.models'];
        $this->app->bind(PermissionContract::class, $config['permission']);
        $this->app->bind(RoleContract::class, $config['role']);
        $this->app->bind(PageContract::class, $config['page']);
    }

    protected function registerBladeExtensions()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('role', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');
                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('elserole', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');
                return "<?php elseif(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });
            $bladeCompiler->directive('hasrole', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');
                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });
            $bladeCompiler->directive('hasanyrole', function ($arguments) {
                list($roles, $guard) = explode(',', $arguments.',');
                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAnyRole({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });
            $bladeCompiler->directive('hasallroles', function ($arguments) {
                list($roles, $guard) = explode(',', $arguments.',');
                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAllRoles({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });
            $bladeCompiler->directive('unlessrole', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');
                return "<?php if(!auth({$guard})->check() || ! auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endunlessrole', function () {
                return '<?php endif; ?>';
            });
        });
    }

    protected function registerMacroHelpers()
    {
        Route::macro('role', function ($roles = []) {
            if (! is_array($roles)) {
                $roles = [$roles];
            }
            $roles = implode('|', $roles);
            $this->middleware("role:$roles");
            return $this;
        });
        Route::macro('permission', function ($permissions = []) {
            if (! is_array($permissions)) {
                $permissions = [$permissions];
            }
            $permissions = implode('|', $permissions);
            $this->middleware("permission:$permissions");
            return $this;
        });
    }
}
