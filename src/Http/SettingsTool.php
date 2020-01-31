<?php

namespace SpaceCode\Maia;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

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
            Image::make('Site Logo', 'site_logo')
                ->path('site')
                ->maxWidth(100)
                ->deletable(false),
            Image::make('Site Favicon', 'site_favicon')
                ->path('site')
                ->maxWidth(196)
                ->deletable(false)
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
