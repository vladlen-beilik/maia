<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SpaceCode\Maia\Traits\Seedable;
use Symfony\Component\Process\Process;

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
        $this->call('vendor:publish', ['--tag' => 'maia-config', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-assets', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-views', '--force' => $this->option('force')]);
        $this->call('vendor:publish', ['--tag' => 'maia-migrations', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-seeds', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'maia-lang', '--force' => true]);
        $this->call('horizon:install');
        $this->call('migrate', ['--force' => true]);
        $this->call('optimize:clear');
        $dumpautoload = new Process(['/usr/local/bin/composer', 'dumpautoload']);
        $dumpautoload->setTimeout(null)->run();
        $this->seed('MaiaDatabaseSeeder');
    }
}