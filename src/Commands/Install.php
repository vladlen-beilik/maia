<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Install extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maia:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Maia resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!file_exists(public_path('storage'))) {
            $this->comment('Creating the symbolic link...');
            $this->call('storage:link');
        }
        $this->callSilent('maia:publish');
        $this->info('Maia scaffolding installed successfully.');
    }
}
