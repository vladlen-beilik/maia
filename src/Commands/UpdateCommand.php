<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
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
     * @return void
     */
    public function handle()
    {
        $update = false;

        $latest = new Process('/usr/local/bin/composer show --latest spacecode-dev/maia');
        $latest->setTimeout(null)->run();
        if($latest->isSuccessful()) {
            $current_version = '';
            $composer_version = '';
            foreach(array_filter(explode("\n", $latest->getOutput()), 'strlen') as $key => $value) {
                if(str_contains($value, 'latest')) {
                    $current_version = Maia::getVersion();
                    $composer_version = trim(explode(':', $value)[1]);
                }
            }
            if ($current_version !== '' || $composer_version !== '') {
                if(intval(explode('.', $current_version)[0]) !== intval(explode('.', $composer_version)[0])) {
                    $update = true;
                } else {
                    if (intval(explode('.', $current_version)[1]) !== intval(explode('.', $composer_version)[1])) {
                        $update = true;
                    } else {
                        if (intval(explode('.', $current_version)[2]) !== intval(explode('.', $composer_version)[2])) {
                            $update = true;
                        }
                    }
                }
            }
            if ($update === true) {
                $upd = new Process('/usr/local/bin/composer update spacecode-dev/maia');
                $upd->setTimeout(null)->run();
                if($upd->isSuccessful()){
                    $this->call('maia::publish');
                    $result = 'Maia successfully updated';
                } else {
                    throw new ProcessFailedException($upd);
                }
            } else {
                $result = 'You have the latest version of CMS Maia';
            }
            $this->info($result);
        } else {
            throw new ProcessFailedException($latest);
        }
    }
}
