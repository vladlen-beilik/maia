<?php

namespace SpaceCode\Maia\Tools;

use Laravel\Nova\Fields\Heading;
use SpaceCode\Maia\Fields\AdvancedImage;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use SpaceCode\Maia\Fields\Tabs;
use SpaceCode\Maia\Fields\Toggle;

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
            (new Tabs(trans('maia::resources.settings'), [
                trans('maia::resources.systemtitle') => [
                    Text::make(trans('maia::resources.system.name'), 'system_name')
                        ->rules('required', function($attribute, $value, $fail) {
                            if ($value === 'Laravel') {
                                return $fail('The field can\'t be `Laravel`, name it differently.');
                            }
                        }),
                    Toggle::make(trans('maia::resources.system.debug'), 'system_debug'),
                ],
                trans('maia::resources.sitetitle') => [
                    Toggle::make(trans('maia::resources.site.index'), 'site_index'),
                    Text::make(trans('maia::resources.site.url'), 'site_url')
                        ->rules('required', function($attribute, $value, $fail) {
                            if ($value === 'http://localhost') {
                                return $fail('The field can\'t be `http://localhost`, name it differently.');
                            }
                        }),
                    Text::make(trans('maia::resources.site.title'), 'site_title'),
                    Text::make(trans('maia::resources.site.excerpt'), 'site_excerpt'),
                    Text::make(trans('maia::resources.site.description'), 'site_description'),
                    AdvancedImage::make(trans('maia::resources.site.logo'), 'site_logo')
                        ->path('site')
                        ->deletable(false),
                    AdvancedImage::make(trans('maia::resources.site.favicon'), 'site_favicon')
                        ->path('site')
                        ->croppable(1/1)
                        ->resize(180)
                        ->deletable(false),
                    Select::make(trans('maia::resources.site.timezone'), 'site_timezone')->options(timezoneList())->displayUsingLabels(),
                ],
                trans('maia::resources.resourcestitle') => [
                    Toggle::make(trans('maia::resources.site.blog'), 'site_blog'),
//                    Toggle::make(trans('maia::resources.site.portfolio'), 'site_portfolio'),
//                    Toggle::make(trans('maia::resources.site.shop'), 'site_shop'),
                ],
                trans('maia::resources.servicestitle') => [
                    Heading::make(trans('maia::resources.services.mailgun.title')),
                    Text::make(trans('maia::resources.services.mailgun.domain'), 'services_mailgun_domain'),
                    Text::make(trans('maia::resources.services.mailgun.secret'), 'services_mailgun_secret'),
                    Text::make(trans('maia::resources.services.mailgun.endpoint'), 'services_mailgun_endpoint'),
                    Heading::make(trans('maia::resources.services.postmark.title')),
                    Text::make(trans('maia::resources.services.postmark.token'), 'services_postmark_token'),
                    Heading::make(trans('maia::resources.services.aws.title')),
                    Text::make(trans('maia::resources.services.aws.key'), 'services_aws_key'),
                    Text::make(trans('maia::resources.services.aws.secret'), 'services_aws_secret'),
                    Text::make(trans('maia::resources.services.aws.region'), 'services_aws_region'),
                    Heading::make(trans('maia::resources.services.pusher.title')),
                    Text::make(trans('maia::resources.services.pusher.key'), 'services_pusher_key'),
                    Text::make(trans('maia::resources.services.pusher.secret'), 'services_pusher_secret'),
                    Text::make(trans('maia::resources.services.pusher.appID'), 'services_pusher_appID'),
                    Text::make(trans('maia::resources.services.pusher.cluster'), 'services_pusher_cluster'),
                    Heading::make(trans('maia::resources.services.memcached.title')),
                    Text::make(trans('maia::resources.services.memcached.persistentID'), 'services_memcached_persistentID'),
                    Text::make(trans('maia::resources.services.memcached.username'), 'services_memcached_username'),
                    Text::make(trans('maia::resources.services.memcached.password'), 'services_memcached_password'),
                    Text::make(trans('maia::resources.services.memcached.host'), 'services_memcached_host'),
                    Text::make(trans('maia::resources.services.memcached.port'), 'services_memcached_port'),
                    Heading::make(trans('maia::resources.services.dynamodb.title')),
                    Text::make(trans('maia::resources.services.aws.key'), 'services_aws_key')->readonly(),
                    Text::make(trans('maia::resources.services.aws.secret'), 'services_aws_secret')->readonly(),
                    Text::make(trans('maia::resources.services.aws.region'), 'services_aws_region')->readonly(),
                    Text::make(trans('maia::resources.services.dynamodb.table'), 'services_dynamodb_table'),
                    Text::make(trans('maia::resources.services.dynamodb.endpoint'), 'services_dynamodb_endpoint'),
                    Heading::make(trans('maia::resources.services.redis.title')),
                    Text::make(trans('maia::resources.services.redis.client'), 'services_redis_client'),
                    Text::make(trans('maia::resources.services.redis.cluster'), 'services_redis_cluster'),
                    Text::make(trans('maia::resources.services.redis.url'), 'services_redis_url'),
                    Text::make(trans('maia::resources.services.redis.host'), 'services_redis_host'),
                    Text::make(trans('maia::resources.services.redis.password'), 'services_redis_password'),
                    Text::make(trans('maia::resources.services.redis.port'), 'services_redis_port'),
                    Text::make(trans('maia::resources.services.redis.database'), 'services_redis_database'),
                    Text::make(trans('maia::resources.services.redis.cachedatabase'), 'services_redis_cache_database'),
                ],
                trans('maia::resources.mailtitle') => [
                    Select::make(trans('maia::resources.mail.driver'), 'mail_driver')->options([
                        'smtp' => 'SMTP',
                        'sendmail' => 'Sendmail',
                        'mailgun' => 'Mailgun',
                        'ses' => 'SES',
                        'postmark' => 'Postmark',
                        'log' => 'Log',
                        'array' => 'Array',
                    ])->displayUsingLabels(),
                    Text::make(trans('maia::resources.mail.host'), 'mail_host'),
                    Select::make(trans('maia::resources.mail.port'), 'mail_port')->options([
                        '25' => '25',
                        '465' => '465',
                        '587' => '587',
                        '2525' => '2525',
                    ])->displayUsingLabels(),
                    Text::make(trans('maia::resources.mail.fromaddress'), 'mail_from_address'),
                    Text::make(trans('maia::resources.mail.fromname'), 'mail_from_name'),
                    Select::make(trans('maia::resources.mail.encryption'), 'mail_encryption')->options([
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                        'starttls' => 'STARTTLS',
                    ])->displayUsingLabels(),
                    Text::make(trans('maia::resources.mail.username'), 'mail_username'),
                    Text::make(trans('maia::resources.mail.password'), 'mail_password'),
                ],
                trans('maia::resources.cachetitle') => [
                    Select::make(trans('maia::resources.cache.driver'), 'cache_driver')->options([
                        'file' => 'File',
                        'apc' => 'Apc',
                        'array' => 'Array',
                        'database' => 'Database',
                        'memcached' => 'Memcached',
                        'redis' => 'Redis',
                        'dynamodb' => 'Dynamodb'
                    ])->displayUsingLabels(),
                ],
                trans('maia::resources.tracking') => [
                    Textarea::make(trans('maia::resources.gtm.head'), 'tracking_google_tag_manager_head'),
                    Textarea::make(trans('maia::resources.gtm.body'), 'tracking_google_tag_manager_body'),
                ]
            ]))
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
