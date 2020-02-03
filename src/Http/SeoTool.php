<?php

namespace SpaceCode\Maia;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Tool;

class SeoTool extends Tool
{
    protected static $fields = [];
    protected static $casts = [];

    public function boot()
    {
        Nova::script('maia-seo', __DIR__ . '/../../dist/js/seo.js');
    }

    public function renderNavigation()
    {
        return view('maia-seo::navigation');
    }

    /**
     * Define settings fields and an optional casts.
     *
     * @param array $fields Array of Nova fields to be displayed.
     * @param array $casts Casts same as Laravel's casts on a model.
     **/
    public static function addSeoFields($fields = [], $casts = [])
    {
        self::$fields = array_merge(self::$fields, $fields ?? []);
        self::$casts = array_merge(self::$casts, $casts ?? []);
    }

    public static function setSeoFields()
    {
        $seo_posts = null;
        if(setting('site_blog')) {
            $seo_posts = new Panel('Posts', [
                Text::make(__('maia::resources.meta_title'), 'seo_posts_meta_title')
                    ->rules('max:55')
                    ->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_posts_meta_description')
                    ->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_posts_meta_keywords')
                    ->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_posts_json_ld')
                    ->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_posts_open_graph')
                    ->help(__('maia::resources.open_graph_text')),
                Toggle::make('Global Index', 'seo_posts_global_index'),
                Toggle::make('Google Bot Index', 'seo_posts_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_posts_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_posts_slurp_bot_index'),
            ]);
        }
        SettingsTool::addSettingsFields([
            new Panel('Home', [
                Text::make(__('maia::resources.meta_title'), 'seo_home_meta_title')
                    ->rules('max:55')
                    ->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_home_meta_description')
                    ->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_home_meta_keywords')
                    ->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_home_json_ld')
                    ->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_home_open_graph')
                    ->help(__('maia::resources.open_graph_text')),
                Toggle::make('Global Index', 'seo_home_global_index'),
                Toggle::make('Google Bot Index', 'seo_home_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_home_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_home_slurp_bot_index'),
            ]),
            new Panel('Pages', [
                Toggle::make('Global Index', 'seo_pages_global_index'),
                Toggle::make('Google Bot Index', 'seo_pages_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_pages_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_pages_slurp_bot_index'),
            ]),
            $seo_posts
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
