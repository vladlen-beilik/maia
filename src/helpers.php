<?php

use SpaceCode\Maia\Models\Settings;

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
