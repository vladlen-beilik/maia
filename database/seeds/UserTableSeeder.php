<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    public function run() {

        /**
         * User
         */
        $developer = User::firstOrCreate(
            ['name' => 'developer'],
            ['fullName' => '{"firstName":"Vladlen","lastName":"Beilik","middleName":null}', 'email' => 'vladlen.beilik@gmail.com', 'password' => bcrypt('password')]
        );
        $developer->assignRole('developer');
        if(is_null($developer->avatar)) {
            $filename = 'avatars/' . Str::random(40) . '.png';
            Storage::disk(config('maia.filemanager.disk'))->copy(public_path('vendor/maia/images/developerAvatar.png'), $filename);
            $developer->update(['avatar' => $filename]);
        }
    }
}
