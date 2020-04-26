<?php

namespace SpaceCode\Maia\Tools;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use SpaceCode\Maia\Fields\Tabs;
use SpaceCode\Maia\Fields\Toggle;

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
        $array = [
            _trans('maia::resources.home') => [
                Text::make(_trans('maia::resources.meta_title'), 'seo_home_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_home_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_home_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_home_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_home_open_graph'),
                Select::make(_trans('maia::resources.document_state'), 'seo_home_document_state')->options(['static' => _trans('maia::resources.static'), 'dynamic' => _trans('maia::resources.dynamic')])->displayUsingLabels(),
                Toggle::make(_trans('maia::resources.robots'), 'seo_home_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_home_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_home_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_home_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_home_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_home_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_home_slurp_bot_index'),
            ]
        ];
        if(isBlog()) {
            $array = Arr::add($array, _trans('maia::resources.posts'), [

                Text::make(_trans('maia::resources.prefixslug'), 'seo_posts_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_posts_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_posts_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_posts_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_posts_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_posts_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_posts_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_posts_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_posts_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_posts_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_posts_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_posts_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_posts_slurp_bot_index'),

            ]);
            $array = Arr::add($array, _trans('maia::resources.postCategories'), [

                Toggle::make(_trans('maia::resources.showindex'), 'seo_post_categories_show_index'),
                Text::make(_trans('maia::resources.prefixslug'), 'seo_post_categories_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_post_categories_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_post_categories_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_post_categories_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_post_categories_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_post_categories_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_post_categories_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_post_categories_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_post_categories_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_post_categories_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_post_categories_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_post_categories_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_post_categories_slurp_bot_index'),

            ]);
            $array = Arr::add($array, _trans('maia::resources.postTags'), [

                Toggle::make(_trans('maia::resources.showindex'), 'seo_post_tags_show_index'),
                Text::make(_trans('maia::resources.prefixslug'), 'seo_post_tags_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_post_tags_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_post_tags_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_post_tags_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_post_tags_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_post_tags_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_post_tags_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_post_tags_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_post_tags_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_post_tags_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_post_tags_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_post_tags_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_post_tags_slurp_bot_index'),

            ]);
        }
        if(isShop()) {
            $array = Arr::add($array, _trans('maia::resources.products'), [

                Text::make(_trans('maia::resources.prefixslug'), 'seo_products_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_products_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_products_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_products_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_products_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_products_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_products_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_products_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_products_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_products_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_products_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_products_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_products_slurp_bot_index'),

            ]);
            $array = Arr::add($array, _trans('maia::resources.productCategories'), [

                Toggle::make(_trans('maia::resources.showindex'), 'seo_product_categories_show_index'),
                Text::make(_trans('maia::resources.prefixslug'), 'seo_product_categories_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_product_categories_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_product_categories_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_product_categories_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_product_categories_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_product_categories_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_product_categories_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_product_categories_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_product_categories_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_product_categories_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_product_categories_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_product_categories_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_product_categories_slurp_bot_index'),

            ]);
            $array = Arr::add($array, _trans('maia::resources.productTags'), [

                Toggle::make(_trans('maia::resources.showindex'), 'seo_product_tags_show_index'),
                Text::make(_trans('maia::resources.prefixslug'), 'seo_product_tags_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_product_tags_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_product_tags_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_product_tags_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_product_tags_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_product_tags_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_product_tags_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_product_tags_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_product_tags_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_product_tags_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_product_tags_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_product_tags_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_product_tags_slurp_bot_index'),

            ]);
            $array = Arr::add($array, _trans('maia::resources.productBrands'), [

                Toggle::make(_trans('maia::resources.showindex'), 'seo_product_brands_show_index'),
                Text::make(_trans('maia::resources.prefixslug'), 'seo_product_brands_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_product_brands_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_product_brands_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_product_brands_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_product_brands_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_product_brands_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_product_brands_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_product_brands_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_product_brands_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_product_brands_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_product_brands_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_product_brands_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_product_brands_slurp_bot_index'),

            ]);
        }
        if(isPortfolio()) {
            $array = Arr::add($array, _trans('maia::resources.portfolio'), [

                Text::make(_trans('maia::resources.prefixslug'), 'seo_portfolio_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_portfolio_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_portfolio_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_portfolio_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_portfolio_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_portfolio_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_portfolio_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_portfolio_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_portfolio_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_portfolio_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_portfolio_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_portfolio_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_portfolio_slurp_bot_index'),

            ]);
            $array = Arr::add($array, _trans('maia::resources.portfolioCategories'), [

                Toggle::make(_trans('maia::resources.showindex'), 'seo_portfolio_categories_show_index'),
                Text::make(_trans('maia::resources.prefixslug'), 'seo_portfolio_categories_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_portfolio_categories_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_portfolio_categories_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_portfolio_categories_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_portfolio_categories_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_portfolio_categories_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_portfolio_categories_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_portfolio_categories_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_portfolio_categories_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_portfolio_categories_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_portfolio_categories_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_portfolio_categories_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_portfolio_categories_slurp_bot_index'),

            ]);
            $array = Arr::add($array, _trans('maia::resources.portfolioTags'), [

                Toggle::make(_trans('maia::resources.showindex'), 'seo_portfolio_tags_show_index'),
                Text::make(_trans('maia::resources.prefixslug'), 'seo_portfolio_tags_prefix')->rules('required'),
                Text::make(_trans('maia::resources.meta_title'), 'seo_portfolio_tags_meta_title')->rules('max:55'),
                Textarea::make(_trans('maia::resources.meta_description'), 'seo_portfolio_tags_meta_description'),
                Textarea::make(_trans('maia::resources.meta_keywords'), 'seo_portfolio_tags_meta_keywords'),
                Textarea::make(_trans('maia::resources.json_ld'), 'seo_portfolio_tags_json_ld'),
                Textarea::make(_trans('maia::resources.open_graph'), 'seo_portfolio_tags_open_graph'),
                Toggle::make(_trans('maia::resources.robots'), 'seo_portfolio_tags_global_index'),
                Toggle::make(_trans('maia::resources.googlebot'), 'seo_portfolio_tags_google_bot_index'),
                Toggle::make(_trans('maia::resources.yandexbot'), 'seo_portfolio_tags_yandex_bot_index'),
                Toggle::make(_trans('maia::resources.bingbot'), 'seo_portfolio_tags_bing_bot_index'),
                Toggle::make(_trans('maia::resources.duckbot'), 'seo_portfolio_tags_duck_bot_index'),
                Toggle::make(_trans('maia::resources.baidubot'), 'seo_portfolio_tags_baidu_bot_index'),
                Toggle::make(_trans('maia::resources.yahoobot'), 'seo_portfolio_tags_slurp_bot_index'),

            ]);
        }
        SeoTool::addSeoFields([
            (new Tabs(_trans('maia::resources.seo'), $array))
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
