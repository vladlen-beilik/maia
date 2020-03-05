<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use SpaceCode\Maia\PermissionRegistrar;

class CacheReset extends Command
{
    protected $signature = 'maia:permission-cache-reset';

    protected $description = 'Reset the permission cache';

    public function handle()
    {
        if (app(PermissionRegistrar::class)->forgetCachedPermissions()) {
            $this->info(trans('maia::commands.cache.flushed'));
        } else {
            $this->error(trans('maia::commands.cache.unable'));
        }
    }
}
