<?php
namespace SpaceCode\Maia;

use Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use SpaceCode\Maia\Contracts\Role as RoleContract;
use SpaceCode\Maia\Contracts\Permission as PermissionContract;

class ToolServiceProvider extends ServiceProvider
{
    public function boot(PermissionRegistrar $permissionLoader, Filesystem $filesystem)
    {
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
        Gate::policy(config('maia.permission.models.permission'), PermissionPolicy::class);
        Gate::policy(config('maia.permission.models.role'), RolePolicy::class);
        Nova::serving(function (ServingNova $event) {
            //
        });
        $this->registerMacroHelpers();
        $this->registerModelBindings();
        $this->registerBladeExtensions();
        $permissionLoader->registerPermissions();
        $this->app->singleton(PermissionRegistrar::class, function ($app) use ($permissionLoader) {
            return $permissionLoader;
        });
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

//        $this->publishes([
//            __DIR__.'/../resources/views/partials' => resource_path('views/vendor/nova/partials'),
//        ], 'nova-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'maia-migrations');
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
        $config = $this->app->config['maia.permission.models'];
        $this->app->bind(PermissionContract::class, $config['permission']);
        $this->app->bind(RoleContract::class, $config['role']);
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
