<?php

use Illuminate\Database\Seeder;
use SpaceCode\Maia\Traits\Seedable;

class MaiaDatabaseSeeder extends Seeder
{
    use Seedable;
    protected $seedersPath = __DIR__ . '/';

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call('PermissionRoleTableSeeder');
         $this->call('UserTableSeeder');
         $this->call('SettingsTableSeeder');
         $this->call('SeoTableSeeder');
    }
}
