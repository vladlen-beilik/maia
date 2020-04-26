<?php
namespace SpaceCode\Maia;

use Gate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use App\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Routing;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Foundation\AliasLoader;
use SpaceCode\Maia\Facades\Maia as MaiaFacade;
use SpaceCode\Maia\Facades\Robots as RobotsFacade;
use SpaceCode\Maia\Javascript\JavaScriptFacade;
use SpaceCode\Maia\JavaScript\LaravelViewBinder;
use SpaceCode\Maia\Javascript\Transformers\Transformer;
use SpaceCode\Maia\Jobs;
use SpaceCode\Maia\Tools;
use SpaceCode\Maia\Contracts;
use SpaceCode\Maia\Middlewares;
use SpaceCode\Maia\Models;

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
            Commands\InstallCommand::class,
            Commands\PublishCommand::class,
            Commands\UpdateCommand::class,
            Commands\DeveloperCommand::class
        ]);
        $this->aliases();
        $this->app->booted(function () {
            $this->routes();
            $this->schedule();
        });
        $this->registerPolicies($user);
        $this->registerTools();
        $this->assets();
        $this->registerMacroHelpers();
        $this->registerModelBindings();
        $this->registerBladeExtensions();
        $permissionLoader->registerPermissions();
        $this->app->singleton(PermissionRegistrar::class, function ($app) use ($permissionLoader) {
            return $permissionLoader;
        });
    }

    protected function aliases() {
        $this->app->alias(
            \SpaceCode\Maia\Controllers\Nova\LoginController::class,
            \Laravel\Nova\Http\Controllers\LoginController::class
        );
        $this->app->alias(
            \SpaceCode\Maia\Controllers\Nova\ForgotPasswordController::class,
            \Laravel\Nova\Http\Controllers\ForgotPasswordController::class
        );
        $this->app->alias(
            \SpaceCode\Maia\Controllers\Nova\ResetPasswordController::class,
            \Laravel\Nova\Http\Controllers\ResetPasswordController::class
        );
    }

    protected function assets() {
        Nova::serving(function () {
//            Nova::script('license', __DIR__ . '/../dist/js/license.js');
            Nova::script('multiselect', __DIR__ . '/../dist/js/multiselect.js');
            Nova::script('tabs', __DIR__ . '/../dist/js/tabs.js');
            Nova::script('slug-field', __DIR__.'/../dist/js/slug.js');
            Nova::script('filemanager-field', __DIR__.'/../dist/js/filemanager-field.js');
            Nova::script('image-field', __DIR__.'/../dist/js/advanced-image.js');
            Nova::script('toggle', __DIR__.'/../dist/js/toggle.js');
            Nova::script('ckeditor5-classic-field', __DIR__.'/../dist/js/editor.js');
            Nova::script('hidden-field', __DIR__.'/../dist/js/hidden.js');
            Nova::script('dependency-container-field', __DIR__.'/../dist/js/dependon.js');
            Nova::script('time', __DIR__ . '/../dist/js/time.js');
            Nova::script('money-field', __DIR__.'/../dist/js/money.js');

            Nova::style('maia-theme', __DIR__ . '/../dist/css/maia.css');
            Nova::style('multiselect', __DIR__ . '/../dist/css/multiselect.css');
            Nova::style('tabs', __DIR__ . '/../dist/css/tabs.css');
            Nova::style('toggle', __DIR__.'/../dist/css/toggle.css');
            Nova::style('ckeditor5-classic-field', __DIR__.'/../dist/css/editor.css');
        });
    }

    protected function schedule() {
        $schedule = app(Schedule::class);
        $schedule->call(function () {
            (new Jobs\PruneStaleAttachments)();
        })->daily();
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
        if(isBlog() || isPortfolio() || isShop()) {
            Gate::policy(Models\Comment::class, Policy\CommentPolicy::class);
        }
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
        if(isShop()) {
            Gate::policy(Models\Shop::class, Policy\ShopPolicy::class);
            if(isActiveShop()) {
                Gate::policy(Models\Product::class, Policy\ProductPolicy::class);
                Gate::policy(Models\ProductCategory::class, Policy\ProductCategoryPolicy::class);
                Gate::policy(Models\ProductTag::class, Policy\ProductTagPolicy::class);
                Gate::policy(Models\ProductBrand::class, Policy\ProductBrandPolicy::class);
            }
        }
        Gate::policy(Models\ContactForm::class, Policy\ContactFormPolicy::class);
    }

    protected function registerTools()
    {
        Nova::tools([
            Tools\FilemanagerTool::make(),
            Tools\NovaHorizonTool::make(),
            Tools\SettingsTool::make(),
            Tools\SeoTool::make(),
            Tools\NovaTool::make()
        ]);
        Tools\SettingsTool::setSettingsFields();
        Tools\SeoTool::setSeoFields();
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
//        Routing::middleware(['nova'])->prefix('nova-vendor/maia-license')->group(__DIR__ . '/../routes/license.php');
        Routing::middleware(['nova', Middlewares\HorizonAuthorize::class])->prefix('nova-vendor/maia-horizon')->group(__DIR__ . '/../routes/horizon.php');
        Routing::middleware(['nova', Middlewares\FilemanagerAuthorize::class])->namespace('SpaceCode\Maia\Controllers')->prefix('nova-vendor/maia-filemanager/nova-filemanager')->group(__DIR__.'/../routes/filemanager.php');
        Routing::middleware(['nova', Middlewares\SettingsAuthorize::class])->group(__DIR__ . '/../routes/settings.php');
        Routing::middleware(['nova', Middlewares\SeoAuthorize::class])->group(__DIR__ . '/../routes/seo.php');
        Routing::middleware(['nova'])->prefix('nova-vendor/ckeditor5-classic')->group(__DIR__ . '/../routes/editor.php');
        Routing::middleware(['web'])->prefix('maia-api')->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('JavaScript', function ($app) {
            return new Transformer(new LaravelViewBinder($app['events'], 'footer'), 'window');
        });
        $this->app->bind('sitemap', function ($app) {
            $config = config('maia.sitemap');
            return new Sitemap($config, $app['Illuminate\Cache\Repository'], $app['config'], $app['files'], $app['Illuminate\Contracts\Routing\ResponseFactory'], $app['view']);
        });
        $loader = AliasLoader::getInstance();
        $loader->alias('JavaScript', JavaScriptFacade::class);
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
        $this->app->bind(Contracts\Permission::class, Models\Permission::class);
        $this->app->bind(Contracts\Role::class, Models\Role::class);
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
