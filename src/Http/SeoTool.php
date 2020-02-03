<?php

namespace SpaceCode\Maia;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Tool;
use Laravel\Nova\Fields\Heading;

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
        $fields = array_filter($fields);
        self::$fields = array_merge(self::$fields, $fields ?? []);
        self::$casts = array_merge(self::$casts, $casts ?? []);
    }

    public static function setSeoFields()
    {
          $seo_posts = null;
          $seo_shop = null;
        if(setting('site_blog')) {
            $seo_posts = (new Panel('Blog', [
                Heading::make('Posts Page'),
                Text::make(__('maia::resources.meta_title'), 'seo_posts_meta_title')->rules('max:55')->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_posts_meta_description')->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_posts_meta_keywords')->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_posts_json_ld')->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_posts_open_graph')->help(__('maia::resources.open_graph_text')),
                Select::make('Document State', 'seo_posts_document_state')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->displayUsingLabels(),
                Toggle::make('Global Index', 'seo_posts_global_index'),
                Toggle::make('Google Bot Index', 'seo_posts_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_posts_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_posts_slurp_bot_index'),

                Heading::make('Post Categories Page'),
                Text::make(__('maia::resources.meta_title'), 'seo_post_categories_meta_title')->rules('max:55')->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_post_categories_meta_description')->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_post_categories_meta_keywords')->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_post_categories_json_ld')->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_post_categories_open_graph')->help(__('maia::resources.open_graph_text')),
                Select::make('Document State', 'seo_post_categories_document_state')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->displayUsingLabels(),
                Toggle::make('Global Index', 'seo_post_categories_global_index'),
                Toggle::make('Google Bot Index', 'seo_post_categories_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_post_categories_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_post_categories_slurp_bot_index'),

                Heading::make('Post Tags Page'),
                Text::make(__('maia::resources.meta_title'), 'seo_post_tags_meta_title')->rules('max:55')->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_post_tags_meta_description')->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_post_tags_meta_keywords')->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_post_tags_json_ld')->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_post_tags_open_graph')->help(__('maia::resources.open_graph_text')),
                Select::make('Document State', 'seo_post_tags_document_state')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->displayUsingLabels(),
                Toggle::make('Global Index', 'seo_post_tags_global_index'),
                Toggle::make('Google Bot Index', 'seo_post_tags_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_post_tags_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_post_tags_slurp_bot_index'),
            ]));
        }
        if(setting('site_shop')) {
            $seo_shop = (new Panel('Shop', [
                Heading::make('Products Page'),
                Text::make(__('maia::resources.meta_title'), 'seo_products_meta_title')->rules('max:55')->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_products_meta_description')->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_products_meta_keywords')->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_products_json_ld')->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_products_open_graph')->help(__('maia::resources.open_graph_text')),
                Select::make('Document State', 'seo_products_document_state')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->displayUsingLabels(),
                Toggle::make('Global Index', 'seo_products_global_index'),
                Toggle::make('Google Bot Index', 'seo_products_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_products_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_products_slurp_bot_index'),

                Heading::make('Product Categories Page'),
                Text::make(__('maia::resources.meta_title'), 'seo_product_categories_meta_title')->rules('max:55')->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_product_categories_meta_description')->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_product_categories_meta_keywords')->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_product_categories_json_ld')->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_product_categories_open_graph')->help(__('maia::resources.open_graph_text')),
                Select::make('Document State', 'seo_product_categories_document_state')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->displayUsingLabels(),
                Toggle::make('Global Index', 'seo_product_categories_global_index'),
                Toggle::make('Google Bot Index', 'seo_product_categories_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_product_categories_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_product_categories_slurp_bot_index'),

                Heading::make('Product Tags Page'),
                Text::make(__('maia::resources.meta_title'), 'seo_product_tags_meta_title')->rules('max:55')->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_product_tags_meta_description')->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_product_tags_meta_keywords')->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_product_tags_json_ld')->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_product_tags_open_graph')->help(__('maia::resources.open_graph_text')),
                Select::make('Document State', 'seo_product_tags_document_state')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->displayUsingLabels(),
                Toggle::make('Global Index', 'seo_product_tags_global_index'),
                Toggle::make('Google Bot Index', 'seo_product_tags_google_bot_index'),
                Toggle::make('Yandex Bot Index', 'seo_product_tags_yandex_bot_index'),
                Toggle::make('Slurp Bot Index', 'seo_product_tags_slurp_bot_index'),
            ]));
        }
        SeoTool::addSeoFields([
            new Panel('Home Page', [
                Text::make(__('maia::resources.meta_title'), 'seo_home_meta_title')->rules('max:55')->help(__('maia::resources.meta_title_text')),
                Textarea::make(__('maia::resources.meta_description'), 'seo_home_meta_description')->help(__('maia::resources.meta_description_text')),
                Textarea::make(__('maia::resources.meta_keywords'), 'seo_home_meta_keywords')->help(__('maia::resources.meta_keywords_text')),
                Textarea::make(__('maia::resources.json_ld'), 'seo_home_json_ld')->help(__('maia::resources.json_ld_text')),
                Textarea::make(__('maia::resources.open_graph'), 'seo_home_open_graph')->help(__('maia::resources.open_graph_text')),
                Select::make('Document State', 'seo_home_document_state')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->displayUsingLabels(),
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
            $seo_posts,
            $seo_shop
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
