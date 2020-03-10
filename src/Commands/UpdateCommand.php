<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use SpaceCode\Maia\Maia;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maia:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update CMS Maia';

    /**
     * Execute the console command.
     *
     * @param Maia $maia
     * @param bool $update
     * @return void
     */
    public function handle(Maia $maia, $update = false)
    {
        $latest = new Process(['/usr/local/bin/composer', 'show', '--latest', 'spacecode-dev/maia']);
        $latest->setTimeout(null)->run();
        if($latest->isSuccessful()) {
            $current_version = $maia->getVersion();
            $composer_version = '0';
            foreach(array_filter(explode("\n", $latest->getOutput()), 'strlen') as $key => $value) {
                if(str_contains($value, 'latest')) {
                    $composer_version = trim(explode(':', $value)[1]);
                }
            }
            if ($current_version < $composer_version) {
                $update = true;
            }
            if ($update) {
                $upd = new Process(['/usr/local/bin/composer', 'update', 'spacecode-dev/maia']);
                $upd->setTimeout(null)->run();
                if($upd->isSuccessful()){
                    $this->call('maia:publish');
                    $this->info('Maia successfully updated');
                } else {
                    throw new ProcessFailedException($upd);
                }
            } else {
                $this->info('You have the latest version of CMS Maia');
            }
        } else {
            throw new ProcessFailedException($latest);
        }
    }
}
