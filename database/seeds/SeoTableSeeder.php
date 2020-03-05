<?php

use Illuminate\Database\Seeder;
use SpaceCode\Maia\Models\Seo;

class SeoTableSeeder extends Seeder
{
    public function run() {
        $settings = [
            'seo_posts_prefix' => 'post',
            'seo_posts_global_index' => 1,
            'seo_posts_google_bot_index' => 1,
            'seo_post_categories_prefix' => 'category',
            'seo_post_categories_global_index' => 1,
            'seo_post_categories_google_bot_index' => 1,
            'seo_post_tags_prefix' => 'tag',
            'seo_post_tags_global_index' => 1,
            'seo_post_tags_google_bot_index' => 1,
            'seo_portfolio_prefix' => 'portfolio',
            'seo_portfolio_global_index' => 1,
            'seo_portfolio_google_bot_index' => 1,
            'seo_portfolio_categories_prefix' => 'portfolio-category',
            'seo_portfolio_categories_global_index' => 1,
            'seo_portfolio_categories_google_bot_index' => 1,
            'seo_portfolio_tags_prefix' => 'portfolio-category',
            'seo_portfolio_tags_global_index' => 1,
            'seo_portfolio_tags_google_bot_index' => 1,
            'seo_products_prefix' => 'product',
            'seo_products_global_index' => 1,
            'seo_products_google_bot_index' => 1,
            'seo_product_categories_prefix' => 'product-category',
            'seo_product_categories_global_index' => 1,
            'seo_product_categories_google_bot_index' => 1,
            'seo_product_tags_prefix' => 'product-category',
            'seo_product_tags_global_index' => 1,
            'seo_product_tags_google_bot_index' => 1,
            'seo_home_document_state' => 'dynamic',
            'seo_home_global_index' => 1,
            'seo_home_google_bot_index' => 1
        ];
        foreach ($settings as $key => $value) {
            $a = Seo::find($key);
            if(!isset($a)) {
                Seo::create(['key' => $key, 'value' => $value]);
            }
        }
    }
}
