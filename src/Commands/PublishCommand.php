<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SpaceCode\Maia\Traits\Seedable;

class PublishCommand extends Command
{
    use Seedable;
    protected $seedersPath = __DIR__ . '/../../database/seeds/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maia:publish {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all of the Maia resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->novaBuild();
        $this->moveStubs();
        $this->call('vendor:publish', ['--tag' => 'maia-config', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-assets', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-views', '--force' => $this->option('force')]);
        $this->call('vendor:publish', ['--tag' => 'maia-migrations', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-seeds', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-lang', '--force' => true]);
        $this->call('horizon:install');
        $this->call('migrate', ['--force' => true]);
        $this->call('view:clear');
        $this->storageBuild();
        $this->seed('MaiaDatabaseSeeder');
    }

    public function moveStubs()
    {
        $stubPath = __DIR__.'/../../stub';
        $array = [
            $stubPath . '/.htaccess.stub' => base_path('.htaccess'),
            $stubPath . '/app/Http/Controllers/MaiaIndexController.php.stub' => app_path('Http/Controllers/MaiaIndexController.php'),
            $stubPath . '/app/Http/Controllers/MaiaRobotsController.php.stub' => app_path('Http/Controllers/MaiaRobotsController.php'),
            $stubPath . '/app/Http/Controllers/MaiaSitemapController.php.stub' => app_path('Http/Controllers/MaiaSitemapController.php'),
            $stubPath . '/app/User.php.stub' => app_path('User.php'),
            $stubPath . '/app/Nova/User.php.stub' => app_path('Nova/User.php'),
            $stubPath . '/app/Providers/NovaServiceProvider.php.stub' => app_path('Providers/NovaServiceProvider.php'),
            $stubPath . '/config/app.php.stub' => config_path('app.php'),
            $stubPath . '/config/nova.php.stub' => config_path('nova.php'),
            $stubPath . '/resources/views/vendor/nova/partials/footer.blade.php.stub' => resource_path('views/vendor/nova/partials/footer.blade.php'),
            $stubPath . '/resources/views/vendor/nova/partials/logo.blade.php.stub' => resource_path('views/vendor/nova/partials/logo.blade.php'),
            $stubPath . '/resources/views/vendor/nova/partials/meta.blade.php.stub' => resource_path('views/vendor/nova/partials/meta.blade.php'),
            $stubPath . '/resources/views/vendor/nova/partials/user.blade.php.stub' => resource_path('views/vendor/nova/partials/user.blade.php')
        ];
        foreach ($array as $key => $value) {
            if($key === $stubPath . '/app/Http/Controllers/MaiaIndexController.php.stub' || $key === $stubPath . '/.htaccess.stub') {
                if(!\File::exists($value)) {
                    (new Filesystem)->copy($key, $value);
                }
            } else {
                (new Filesystem)->copy($key, $value);
            }
        }
    }

    public function novaBuild() {
        if(!\File::exists(app_path('Nova'))) {
            $this->call('nova:install');
        } else {
            $this->call('nova:publish');
        }
    }

    public function storageBuild() {
        if (!\File::exists(public_path('storage'))) {
            $this->call('storage:link');
        }
    }
}
