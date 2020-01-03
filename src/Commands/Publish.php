<?php

namespace Laravel\Nova\Console;

use Illuminate\Console\Command;

class Publish extends Command
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
    protected $description = 'Publish all of the Ьфшф resources';

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
        $this->call('vendor:publish', [
            '--tag' => 'maia-assets',
            '--force' => true,
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'maia-lang',
            '--force' => $this->option('force'),
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'maia-views',
            '--force' => $this->option('force'),
        ]);
        $this->call('migrate');
        $this->call('view:clear');
    }
}
