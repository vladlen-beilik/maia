<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SpaceCode\Maia\PermissionRegistrar;
use SpaceCode\Maia\MaiaServiceProvider;
use Symfony\Component\Process\Process;

class Install extends Command
{
    protected $signature = 'maia:install';

    protected $description = 'Install an application';

    /**
     * @param Filesystem $filesystem
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    /**
     * @param Filesystem $filesystem
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Filesystem $filesystem)
    {
        if (!file_exists(public_path('storage'))) {
            $this->call('storage:link');
        }
        $this->call('vendor:publish', ['--provider' => MaiaServiceProvider::class, '--force' => true]);
        $this->call('migrate');
        $dump_autoload = new Process('/usr/local/bin/composer dump-autoload -o');
        $dump_autoload->run();
        $this->info('Maia successfully installed.');
    }
}
