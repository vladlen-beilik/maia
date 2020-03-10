<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run() {
        $developer = User::firstOrCreate(
            ['name' => 'developer'],
            ['fullName' => '{"firstName":"Vladlen","lastName":"Beilik","middleName":null}', 'email' => 'vladlen.beilik@gmail.com', 'password' => bcrypt('password')]
        );
        $developer->assignRole('developer');
    }
}
