<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SpaceCode\Maia\Traits\Seedable;

class PublishCommand extends Command
{
    use Seedable;
    protected $seedersPath = __DIR__ . '/../../../database/seeds/';

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
        $this->moveStubs();

        $this->call('vendor:publish', [
            '--tag' => 'maia-config',
            '--force' => true,
        ]);

//        $this->call('vendor:publish', [
//            '--tag' => 'maia-assets',
//            '--force' => true,
//        ]);

        $this->call('vendor:publish', [
            '--tag' => 'maia-views',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'maia-migrations',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'maia-seeds',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'maia-lang',
            '--force' => $this->option('force'),
        ]);

        $this->seed('MaiaDatabaseSeeder');
        $this->call('view:clear');
        $this->call('migrate');
    }

    public function moveStubs()
    {
        $stubPath = __DIR__.'/../../stub';
        (new Filesystem)->copy($stubPath.'/app/User.php.stub', app_path('User.php'));
        (new Filesystem)->copy($stubPath.'/app/Nova/User.php.stub', app_path('Nova/User.php'));
        (new Filesystem)->copy($stubPath.'/app/Providers/NovaServiceProvider.php.stub', app_path('Providers/NovaServiceProvider.php'));
        (new Filesystem)->copy($stubPath.'/config/app.php.stub', config_path('app.php'));
        (new Filesystem)->copy($stubPath.'/config/nova.php.stub', config_path('nova.php'));
    }
}
