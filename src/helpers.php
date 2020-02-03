<?php

use Illuminate\Support\Str;
use SpaceCode\Maia\Models\Settings;
use SpaceCode\Maia\Models\Seo;

if (!function_exists('settings')) {
    function settings($keys = null)
    {
        $query = Settings::query();
        if (isset($keys)) $query->whereIn('key', $keys);
        return $query->get()->pluck('value', 'key')->toArray();
    }
}

if (!function_exists('setting')) {
    function setting($key)
    {
        $setting = Settings::find($key);
        return isset($setting) ? $setting->value : null;
    }
}

if (!function_exists('seo')) {
    function seo($key)
    {
        $seo = Seo::find($key);
        return isset($seo) ? $seo->value : null;
    }
}

if (! function_exists('getModelForGuard')) {
    /**
     * @param string $guard
     *
     * @return string|null
     */
    function getModelForGuard(string $guard)
    {
        return collect(config('auth.guards'))
            ->map(function ($guard) {
                if (! isset($guard['provider'])) {
                    return;
                }
                return config("auth.providers.{$guard['provider']}.model");
            })->get($guard);
    }
}

if (! function_exists('timezoneList')) {
    function timezoneList() {
        $timezones = [];
        foreach([DateTimeZone::AFRICA, DateTimeZone::AMERICA, DateTimeZone::ANTARCTICA, DateTimeZone::ASIA, DateTimeZone::ATLANTIC, DateTimeZone::AUSTRALIA, DateTimeZone::EUROPE, DateTimeZone::INDIAN, DateTimeZone::PACIFIC] as $region ) {
            $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
        }
        $timezone_offsets = [];
        foreach( $timezones as $timezone ) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }
        asort($timezone_offsets);
        $timezone_list = [];
        foreach($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );
            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
            $pretty_group = explode('/', $timezone)[0];
            $pretty_label = str_replace('_', ' ', $timezone) . ' ' . $pretty_offset;
            $timezone_list['UTC'] = ['label' => 'UTC', 'group' => 'Custom'];
            $timezone_list[$timezone] = ['label' => $pretty_label, 'group' => $pretty_group];
        }
        if (!Cache::has('timezone_list')) {
            Cache::forever('timezone_list', $timezone_list);
        }
        return Cache::get('timezone_list');
    }
}

if (! function_exists('changeEnv')) {
    function changeEnv($change_key, $change_value) {
        if(isset($change_key) && isset($change_value)) {
            $env = file_get_contents(base_path() . '/.env');
            $new_env = [];
            foreach(explode(PHP_EOL, $env) as $key => $value) {
                $entry = explode("=", $value, 2);
                if(empty($value)) {
                    $new_env[] = '';
                } else {
                    if($entry[0] === $change_key) {
                        if(Str::contains($change_value, ' ') || Str::contains($change_value, '{') && Str::contains($change_value, '}') && Str::contains($change_value, '$')) {
                            $new_env[] = $entry[0] . '="' . $change_value . '"';
                        } else {
                            $new_env[] = $entry[0] . '=' . $change_value;
                        }
                    } else {
                        $new_env[] = $entry[0] . '=' . $entry[1];
                    }
                }
            }
            file_put_contents(base_path() . '/.env', implode(PHP_EOL, $new_env));
        }
    }
}


