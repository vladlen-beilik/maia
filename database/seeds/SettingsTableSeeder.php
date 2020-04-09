<?php

use Illuminate\Database\Seeder;
use SpaceCode\Maia\Models\Settings;

class SettingsTableSeeder extends Seeder
{
    public function run() {

        $settings = [
            'system_name' => 'Laravel',
            'system_debug' => 0,
            'site_url' => url(''),
            'site_timezone' => 'UTC',
            'services_mailgun_endpoint' => 'api.mailgun.net',
            'services_aws_region' => 'us-east-1',
            'services_memcached_host' => '127.0.0.1',
            'services_memcached_port' => '11211',
            'services_dynamodb_table' => 'cache',
            'services_redis_client' => 'phpredis',
            'services_redis_cluster' => 'redis',
            'services_redis_host' => '127.0.0.1',
            'services_redis_port' => '6379',
            'services_redis_database' => '0',
            'services_redis_cache_database' => '1',
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.mailgun.org',
            'mail_port' => '587',
            'mail_from_address' => 'hello@example.com',
            'mail_from_name' => 'Example',
            'mail_encryption' => 'tls',
            'cache_driver' => 'file',
            'site_blog' => 1,
            'site_portfolio' => 0,
            'site_shop' => 0,
            'comments_confirmed' => 1,
            'comments_userLoggedIn' => 1,
            'comments_autoClose' => 7,
            'comments_nested' => 3,
            'comments_display' => 'older',
            'shops_prefix' => 'shop'
        ];
        foreach ($settings as $key => $value) {
            $a = Settings::find($key);
            if(!isset($a)) {
                Settings::create(['key' => $key, 'value' => $value]);
            }
        }
    }
}
