<?php

namespace SpaceCode\Maia;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Tool;
use DateTime;
use DateTimeZone;

class SettingsTool extends Tool
{
    protected static $fields = [];
    protected static $casts = [];

    public function boot()
    {
        Nova::script('maia-settings', __DIR__ . '/../../dist/js/settings.js');
    }

    public function renderNavigation()
    {
        return view('maia-settings::navigation');
    }

    /**
     * Define settings fields and an optional casts.
     *
     * @param array $fields Array of Nova fields to be displayed.
     * @param array $casts Casts same as Laravel's casts on a model.
     **/
    public static function addSettingsFields($fields = [], $casts = [])
    {
        self::$fields = array_merge(self::$fields, $fields ?? []);
        self::$casts = array_merge(self::$casts, $casts ?? []);
    }

    public static function setSettingsFields()
    {
        SettingsTool::addSettingsFields([
            Text::make('Site Title', 'site_title'),
            Text::make('Site Excerpt', 'site_excerpt'),
            Text::make('Site Description', 'site_description'),
            AdvancedImage::make('Site Logo', 'site_logo')
                ->path('site')
                ->croppable(1/1)
                ->resize(196)
                ->deletable(false),
            AdvancedImage::make('Site Favicon', 'site_favicon')
                ->path('site')
                ->croppable(1/1)
                ->resize(196)
                ->deletable(false),
            Select::make('Site Timezone', 'site_timezone')->options(timezoneList())->displayUsingLabels(),
            Toggle::make('Site Index', 'site_index'),
            Toggle::make('Site Blog', 'site_blog'),
            Toggle::make('Site Shop', 'site_shop'),
            new Panel('Services Settings', [
                Text::make('Mailgun Domain', 'services_mailgun_domain'),
                Text::make('Mailgun Secret', 'services_mailgun_secret'),
                Text::make('Mailgun Endpoint', 'services_mailgun_endpoint'),
                Text::make('Postmark Token', 'services_postmark_token'),
                Text::make('AWS Key', 'services_aws_key'),
                Text::make('AWS Secret', 'services_aws_secret'),
                Text::make('AWS Region', 'services_aws_region'),
            ]),
            new Panel('Mail Settings', [
                Select::make('Mail Driver', 'mail_driver')->options([
                    'smtp' => 'SMTP',
                    'sendmail' => 'Sendmail',
                    'mailgun' => 'Mailgun',
                    'ses' => 'SES',
                    'postmark' => 'Postmark',
                    'log' => 'Log',
                    'array' => 'Array',
                ])->displayUsingLabels(),
                Text::make('Mail Host', 'mail_host'),
                Select::make('Mail Port', 'mail_port')->options([
                    '25' => '25',
                    '465' => '465',
                    '587' => '587',
                    '2525' => '2525',
                ])->displayUsingLabels(),
                Text::make('Mail From Address', 'mail_from_address'),
                Text::make('Mail From Name', 'mail_from_name'),
                Select::make('Mail Encryption', 'mail_encryption')->options([
                    'ssl' => 'SSL',
                    'tls' => 'TLS',
                    'starttls' => 'STARTTLS',
                ])->displayUsingLabels(),
                Text::make('Mail Username', 'mail_username'),
                Text::make('Mail Password', 'mail_password'),
            ]),
            new Panel('Tracking Settings', [
                Textarea::make('Google Tag Manager Head', 'tracking_google_tag_manager_head'),
                Textarea::make('Google Tag Manager Body', 'tracking_google_tag_manager_body'),
            ])
        ]);
    }

    /**
     * Define casts.
     *
     * @param array $casts Casts same as Laravel's casts on a model.
     **/
    public static function addCasts($casts = [])
    {
        self::$casts = array_merge(self::$casts, $casts);
    }

    public static function getFields()
    {
        return self::$fields;
    }

    public static function getCasts()
    {
        return self::$casts;
    }
}
