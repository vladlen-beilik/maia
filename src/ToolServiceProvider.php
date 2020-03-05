<?php
namespace SpaceCode\Maia;

use Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use SpaceCode\Maia\Models;
use App\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Routing;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use SpaceCode\Maia\Contracts\Role as RoleContract;
use SpaceCode\Maia\Contracts\Permission as PermissionContract;
use SpaceCode\Maia\Contracts\Page as PageContract;
use SpaceCode\Maia\Contracts\Post as PostContract;
use SpaceCode\Maia\Contracts\PostCategory as PostCategoryContract;
use SpaceCode\Maia\Contracts\PostTag as PostTagContract;
use SpaceCode\Maia\Contracts\Portfolio as PortfolioContract;
use SpaceCode\Maia\Contracts\PortfolioCategory as PortfolioCategoryContract;
use SpaceCode\Maia\Contracts\PortfolioTag as PortfolioTagContract;
use SpaceCode\Maia\Contracts\ContactForm as ContactFormContract;
use SpaceCode\Maia\Middlewares\FilemanagerAuthorize;
use SpaceCode\Maia\Middlewares\SettingsAuthorize;
use SpaceCode\Maia\Middlewares\SeoAuthorize;
use SpaceCode\Maia\Middlewares\HorizonAuthorize;
use Illuminate\Foundation\AliasLoader;
use SpaceCode\Maia\Facades\Maia as MaiaFacade;
use SpaceCode\Maia\Facades\Robots as RobotsFacade;
use Spatie\Crawler\Crawler;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * @param PermissionRegistrar $permissionLoader
     * @param Filesystem $filesystem
     * @param User $user
     * @param ServingNova $event
     */
    public function boot(PermissionRegistrar $permissionLoader, Filesystem $filesystem, User $user, ServingNova $event)
    {

        $this->loadViewsFrom(__DIR__.'/../resources/views/sitemap', 'maia-sitemap');
        $this->loadViewsFrom(__DIR__.'/../resources/views/filemanager', 'maia-filemanager');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/settings', 'maia-settings');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/seo', 'maia-seo');
        $this->loadViewsFrom(__DIR__.'/../resources/views/horizon', 'maia-horizon');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'maia');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'maia');

        $this->loadHelper();
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
        Nova::serving(function ($event) {
            Nova::script('tabs', __DIR__ . '/../dist/js/tabs.js');
            Nova::style('tabs', __DIR__ . '/../dist/css/tabs.css');
            Nova::script('maia-sluggable', __DIR__.'/../dist/js/sluggable.js');
            Nova::script('filemanager-field', __DIR__.'/../dist/js/filemanager-field.js');
            Nova::script('image-field', __DIR__.'/../dist/js/advanced-image.js');
            Nova::script('toggle', __DIR__.'/../dist/js/toggle.js');
            Nova::style('toggle', __DIR__.'/../dist/css/toggle.css');
        });
        $this->registerMacroHelpers();
        $this->registerModelBindings();
        $this->registerBladeExtensions();
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
        Gate::policy(\App\User::class, Policy\UserPolicy::class);
        Gate::policy(Models\Permission::class, Policy\PermissionPolicy::class);
        Gate::policy(Models\Role::class, Policy\RolePolicy::class);
        Gate::policy(Models\Page::class, Policy\PagePolicy::class);
        if(isBlog()) {
            Gate::policy(Models\Post::class, Policy\PostPolicy::class);
            Gate::policy(Models\PostCategory::class, Policy\PostCategoryPolicy::class);
            Gate::policy(Models\PostTag::class, Policy\PostTagPolicy::class);
        }
        if(isPortfolio()) {
            Gate::policy(Models\Portfolio::class, Policy\PortfolioPolicy::class);
            Gate::policy(Models\PortfolioCategory::class, Policy\PortfolioCategoryPolicy::class);
            Gate::policy(Models\PortfolioTag::class, Policy\PortfolioTagPolicy::class);
        }
        Gate::policy(Models\ContactForm::class, Policy\ContactFormPolicy::class);
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([__DIR__.'/../config/maia.php' => config_path('maia.php')], 'maia-config');
        $this->publishes([__DIR__.'/../dist/index' => public_path('vendor/maia')], 'maia-assets');
        $this->publishes([__DIR__.'/../resources/styles/sitemap' => public_path('vendor/sitemap')], 'maia-assets');
        $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang/vendor/maia')], 'maia-lang');
        $this->publishes([__DIR__.'/../resources/views/index' => resource_path('views')], 'maia-views');
        $this->publishes([__DIR__.'/../resources/views/sitemap' => base_path('resources/views/vendor/sitemap')], 'maia-views');
        $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'maia-migrations');
        $this->publishes([__DIR__.'/../database/seeds' => database_path('seeds')], 'maia-seeds');
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
        Routing::middleware(['nova', HorizonAuthorize::class])->prefix('nova-vendor/maia-horizon')->group(__DIR__ . '/../routes/horizon.php');
        Routing::middleware(['nova'])->prefix('nova-vendor/maia-sluggable')->group(__DIR__.'/../routes/sluggable.php');
        Routing::middleware(['nova', FilemanagerAuthorize::class])->namespace('SpaceCode\Maia\Controllers')->prefix('nova-vendor/maia-filemanager/nova-filemanager')->group(__DIR__.'/../routes/filemanager.php');
        Routing::middleware(['nova', SettingsAuthorize::class])->group(__DIR__ . '/../routes/settings.php');
        Routing::middleware(['nova', SeoAuthorize::class])->group(__DIR__ . '/../routes/seo.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('sitemap', function ($app) {
            $config = config('maia.sitemap');
            return new Sitemap(
                $config,
                $app['Illuminate\Cache\Repository'],
                $app['config'],
                $app['files'],
                $app['Illuminate\Contracts\Routing\ResponseFactory'],
                $app['view']
            );
        });
        $loader = AliasLoader::getInstance();
        $loader->alias('sitemap', Sitemap::class);
        $loader->alias('Maia', MaiaFacade::class);
        $this->app->booting(function($loader) {
            $loader->alias('Robots', RobotsFacade::class);
        });
        $this->app->singleton('maia', function () {
            return new Maia();
        });
        $this->app->singleton('robots', function () {
            return new Robots();
        });
    }

    protected function registerModelBindings()
    {
        $this->app->bind(PermissionContract::class, Models\Permission::class);
        $this->app->bind(RoleContract::class, Models\Role::class);
        $this->app->bind(PageContract::class, Models\Page::class);
        if(isBlog()) {
            $this->app->bind(PostContract::class, Models\Post::class);
            $this->app->bind(PostCategoryContract::class, Models\PostCategory::class);
            $this->app->bind(PostTagContract::class, Models\PostTag::class);
        }
        if(isPortfolio()) {
            $this->app->bind(PortfolioContract::class, Models\Portfolio::class);
            $this->app->bind(PortfolioCategoryContract::class, Models\PortfolioCategory::class);
            $this->app->bind(PortfolioTagContract::class, Models\PortfolioTag::class);
        }
        $this->app->bind(ContactFormContract::class, Models\ContactForm::class);
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sitemap', Sitemap::class];
    }
}
