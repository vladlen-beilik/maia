<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
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
        $this->call('vendor:publish', [
            '--tag' => 'maia-config',
            '--force' => $this->option('force'),
        ]);

//        $this->call('vendor:publish', [
//            '--tag' => 'maia-assets',
//            '--force' => true,
//        ]);

        $this->call('vendor:publish', [
            '--tag' => 'maia-migrations',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'maia-lang',
            '--force' => $this->option('force'),
        ]);

//        $this->call('vendor:publish', [
//            '--tag' => 'nova-views',
//            '--force' => $this->option('force'),
//        ]);

        $this->call('view:clear');
        $this->call('migrate');
    }
}
