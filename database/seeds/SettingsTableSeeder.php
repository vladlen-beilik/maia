<?php

use Illuminate\Database\Seeder;
use SpaceCode\Maia\Models\Settings;

class SettingsTableSeeder extends Seeder
{
    public function run() {
        is_null(setting('site_timezone')) ? Settings::create(['key' => 'site_timezone', 'value' => 'UTC']) : '';
        is_null(setting('mail_driver')) ? Settings::create(['key' => 'mail_driver', 'value' => 'smtp']) : '';
        is_null(setting('mail_host')) ? Settings::create(['key' => 'mail_host', 'value' => 'smtp.mailgun.org']) : '';
        is_null(setting('mail_port')) ? Settings::create(['key' => 'mail_port', 'value' => '587']) : '';
        is_null(setting('mail_from_address')) ? Settings::create(['key' => 'mail_from_address', 'value' => 'hello@example.com']) : '';
        is_null(setting('mail_from_name')) ? Settings::create(['key' => 'mail_from_name', 'value' => 'Example']) : '';
        is_null(setting('mail_encryption')) ? Settings::create(['key' => 'mail_encryption', 'value' => 'tls']) : '';
        is_null(setting('services_mailgun_endpoint')) ? Settings::create(['key' => 'services_mailgun_endpoint', 'value' => 'api.mailgun.net']) : '';
        is_null(setting('services_aws_region')) ? Settings::create(['key' => 'services_aws_region', 'value' => 'us-east-1']) : '';
        is_null(setting('site_blog')) ? Settings::create(['key' => 'site_blog', 'value' => 1]) : '';
        is_null(setting('site_shop')) ? Settings::create(['key' => 'site_shop', 'value' => 0]) : '';
    }
}
