<?php

namespace SpaceCode\Maia\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SpaceCode\Maia\Models\Settings;
use SpaceCode\Maia\Tools\SettingsTool;
use Intervention\Image\Facades\Image;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\ResolvesFields;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Laravel\Nova\Fields\FieldCollection;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    use ResolvesFields;
    use ConditionallyLoadsAttributes;

    public function get(Request $request)
    {
        $fields = $this->assignToPanels(_trans('maia::resources.settings'), $this->availableFields());
        $panels = $this->panelsWithDefaultLabel(_trans('maia::resources.settings'), new NovaRequest);

        $addResolveCallback = function (&$field) {
            if($field->component === 'dependency-container-field') {
                foreach ($field->meta['fields'] as $meta) {
                    if (!empty($meta->attribute)) {
                        $setting = Settings::where('key', $meta->attribute)->first();
                        $meta->resolve([$meta->attribute => isset($setting) ? $setting->value : '']);
                    }
                }
            } else {
                if (!empty($field->attribute)) {
                    $setting = Settings::where('key', $field->attribute)->first();
                    $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
                }
            }
        };

        $fields->each(function (&$field) use ($addResolveCallback) {
            $addResolveCallback($field);
        });

        return response()->json([
            'panels' => $panels,
            'fields' => $fields,
        ], 200);
    }

    public function save(NovaRequest $request)
    {
        $fields = $this->availableSaveFields();

        $rules = [];
        foreach ($fields as $field) {
            $fakeResource = new \stdClass;
            $fakeResource->{$field->attribute} = setting($field->attribute);
            $field->resolve($fakeResource, $field->attribute); // For nova-translatable support
            $rules = array_merge($rules, $field->getUpdateRules($request));
        }

        Validator::make($request->all(), $rules)->validate();

        $fields->whereInstanceOf(Resolvable::class)->each(function ($field) use ($request) {
            if (empty($field->attribute)) return;

            $existingRow = Settings::where('key', $field->attribute)->first();

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (!property_exists($tempResource, $field->attribute)) return;

            if (isset($existingRow)) {
                if($field->attribute === 'site_favicon' && $existingRow->value !== $tempResource->{$field->attribute}) {
                    $existing_array = explode('.', $existingRow->value);
                    $existing_mime = $existing_array[sizeof($existing_array) - 1];
                    $existing_path = str_replace('.' . $existing_mime,'', $existingRow->value);
                    $temp_array = explode('.', $tempResource->{$field->attribute});
                    $temp_mime = $temp_array[sizeof($temp_array) - 1];
                    $temp_path = str_replace('.' . $temp_mime,'', $tempResource->{$field->attribute});
                    foreach (['32', '180'] as $size) {
                        Storage::disk(config('maia.filemanager.disk'))->delete($existing_path . '_' . $size . '.' . $existing_mime);
                        Storage::disk(config('maia.filemanager.disk'))->copy($tempResource->{$field->attribute}, $temp_path . '_' . $size . '.' . $temp_mime);
                        $image = Image::make(Storage::disk(config('maia.filemanager.disk'))->get($temp_path . '_' . $size . '.' . $temp_mime));
                        $image->resize($size, $size);
                        $image->save(public_path('storage/' . $temp_path . '_' . $size . '.' . $temp_mime));
                    }
                }
                $existingRow->update(['value' => $tempResource->{$field->attribute}]);
            } else {
                if($field->attribute === 'site_favicon') {
                    $temp_array = explode('.', $tempResource->{$field->attribute});
                    $temp_mime = $temp_array[sizeof($temp_array) - 1];
                    $temp_path = str_replace('.' . $temp_mime,'', $tempResource->{$field->attribute});
                    foreach (['32', '180'] as $size) {
                        Storage::disk(config('maia.filemanager.disk'))->copy($tempResource->{$field->attribute}, $temp_path . '_' . $size . '.' . $temp_mime);
                        $image = Image::make(Storage::disk(config('maia.filemanager.disk'))->get($temp_path . '_' . $size . '.' . $temp_mime));
                        $image->resize($size, $size);
                        $image->save(public_path('storage/' . $temp_path . '_' . $size . '.' . $temp_mime));
                    }
                }
                Settings::create([
                    'key' => $field->attribute,
                    'value' => $tempResource->{$field->attribute},
                ]);
            }

            $settingsArray = [
                'system_name' => 'APP_NAME', // 'Laravel'
                'system_debug' => 'APP_DEBUG', // false
                'site_url' => 'APP_URL', // http://localhost
                'site_timezone' => 'APP_TIMEZONE', // UTC
                'services_mailgun_domain' => 'MAILGUN_DOMAIN',
                'services_mailgun_secret' => 'MAILGUN_SECRET',
                'services_mailgun_endpoint' => 'MAILGUN_ENDPOINT', // api.mailgun.net
                'services_postmark_token' => 'POSTMARK_TOKEN',
                'services_aws_key' => 'AWS_ACCESS_KEY_ID',
                'services_aws_secret' => 'AWS_SECRET_ACCESS_KEY',
                'services_aws_region' => 'AWS_DEFAULT_REGION', // us-east-1
                'services_pusher_key' => 'PUSHER_APP_KEY',
                'services_pusher_secret' => 'PUSHER_APP_SECRET',
                'services_pusher_appID' => 'PUSHER_APP_ID',
                'services_pusher_cluster' => 'PUSHER_APP_CLUSTER',
                'services_memcached_persistentID' => 'MEMCACHED_PERSISTENT_ID',
                'services_memcached_username' => 'MEMCACHED_USERNAME',
                'services_memcached_password' => 'MEMCACHED_PASSWORD',
                'services_memcached_host' => 'MEMCACHED_HOST', // 127.0.0.1
                'services_memcached_port' => 'MEMCACHED_PORT', // 11211
                'services_dynamodb_table' => 'DYNAMODB_CACHE_TABLE', // cache
                'services_dynamodb_endpoint' => 'DYNAMODB_ENDPOINT',
//                'services_redis_client' => 'REDIS_CLIENT', // phpredis
                'services_redis_cluster' => 'REDIS_CLUSTER', // redis
                'services_redis_url' => 'REDIS_URL',
                'services_redis_host' => 'REDIS_HOST', // 127.0.0.1
                'services_redis_password' => 'REDIS_PASSWORD',
                'services_redis_port' => 'REDIS_PORT', // 6379
                'services_redis_database' => 'REDIS_DB', // 0
                'services_redis_cache_database' => 'REDIS_CACHE_DB', // 1
                'mail_driver' => 'MAIL_DRIVER', // smtp
                'mail_host' => 'MAIL_HOST', // smtp.mailgun.org
                'mail_port' => 'MAIL_PORT', // 587
                'mail_from_address' => 'MAIL_FROM_ADDRESS', // hello@example.com
                'mail_from_name' => 'MAIL_FROM_NAME', // Example
                'mail_encryption' => 'MAIL_ENCRYPTION', // tls
                'mail_username' => 'MAIL_USERNAME',
                'mail_password' => 'MAIL_PASSWORD',
                'cache_driver' => 'CACHE_DRIVER' // file
            ];

            if(array_key_exists($field->attribute, $settingsArray)) {
                $temp = $tempResource->{$field->attribute};
                $env = file_get_contents(base_path() . '/.env');
                if(!Str::contains($env, $settingsArray[$field->attribute])) {
                    if(Str::contains($temp, ' ') || Str::contains($temp, '{') && Str::contains($temp, '}') && Str::contains($temp, '$')) {
                        $envString = $settingsArray[$field->attribute] . '="' . $temp . '"';
                    } else {
                        $envString = is_null($temp) ? $settingsArray[$field->attribute] . '=null' : $settingsArray[$field->attribute] . '=' . $temp;
                    }
                    setEnv($envString);
                } else {
                    changeEnv($settingsArray[$field->attribute], $temp);
                }
            }

            if($field->attribute === 'site_url' && !Str::contains(file_get_contents(base_path() . '/.env'), 'HORIZON_PREFIX')) {
                $envString = 'HORIZON_PREFIX=' . str_replace('.', '-', class_basename($tempResource->{$field->attribute})) . '-horizon:';
                setEnv($envString);
            }

            if($field->attribute === 'site_blog' || $field->attribute === 'site_portfolio' || $field->attribute === 'site_shop' || $field->attribute === 'site_index' || $field->attribute === 'site_favicon') {
                $explode = explode('_', $field->attribute);
                $name = $explode[0].ucfirst($explode[1]);
                if (Cache::has($name)) {
                    Cache::forget($name);
                }
                Cache::forever($name, $field->attribute === 'site_favicon' ? $tempResource->{$field->attribute} : boolval($tempResource->{$field->attribute}));
            }
        });
        rebuildEnv();
        return response('', 204);
    }

    public function deleteValue(Request $request, $fieldName)
    {
        $existingRow = Settings::where('key', $fieldName)->first();
        if (isset($existingRow)) $existingRow->update(['value' => null]);
        return response('', 204);
    }

    public function putTrue(Request $request, $fieldName)
    {
        $existingRow = Settings::where('key', $fieldName)->first();
        if (isset($existingRow)) $existingRow->update(['value' => 1]);
        return response('', 204);
    }

    protected function availableFields()
    {
        return new FieldCollection(($this->filter(SettingsTool::getFields())));
    }

    protected function availableSaveFields()
    {
        $collection = collect([]);
        foreach ($this->filter(SettingsTool::getFields()) as $field) {
            if($field->component === 'dependency-container-field') {
                foreach ($field->meta['fields'] as $meta) {
                    $collection->push($meta);
                }
            }
            $collection->push($field);
        }
        return new FieldCollection($collection);
    }

    protected function fields(Request $request)
    {
        return SettingsTool::getFields();
    }
}