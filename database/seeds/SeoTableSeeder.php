<?php

use Illuminate\Database\Seeder;
use SpaceCode\Maia\Models\Seo;

class SeoTableSeeder extends Seeder
{
    public function run() {
        is_null(seo('seo_posts_document_state')) ? Seo::create(['key' => 'seo_posts_document_state', 'value' => 'dynamic']) : '';
        is_null(seo('seo_posts_global_index')) ? Seo::create(['key' => 'seo_posts_global_index', 'value' => 0]) : '';
        is_null(seo('seo_post_categories_document_state')) ? Seo::create(['key' => 'seo_post_categories_document_state', 'value' => 'dynamic']) : '';
        is_null(seo('seo_post_categories_global_index')) ? Seo::create(['key' => 'seo_post_categories_global_index', 'value' => 0]) : '';
        is_null(seo('seo_post_tags_document_state')) ? Seo::create(['key' => 'seo_post_tags_document_state', 'value' => 'dynamic']) : '';
        is_null(seo('seo_post_tags_global_index')) ? Seo::create(['key' => 'seo_post_tagss_global_index', 'value' => 0]) : '';

        is_null(seo('seo_products_document_state')) ? Seo::create(['key' => 'seo_products_document_state', 'value' => 'dynamic']) : '';
        is_null(seo('seo_products_global_index')) ? Seo::create(['key' => 'seo_products_global_index', 'value' => 0]) : '';
        is_null(seo('seo_product_categories_document_state')) ? Seo::create(['key' => 'seo_product_categories_document_state', 'value' => 'dynamic']) : '';
        is_null(seo('seo_product_categories_global_index')) ? Seo::create(['key' => 'seo_product_categories_global_index', 'value' => 0]) : '';
        is_null(seo('seo_product_tags_document_state')) ? Seo::create(['key' => 'seo_product_tags_document_state', 'value' => 'dynamic']) : '';
        is_null(seo('seo_product_tags_global_index')) ? Seo::create(['key' => 'seo_product_tags_global_index', 'value' => 0]) : '';

        is_null(seo('seo_home_document_state')) ? Seo::create(['key' => 'seo_home_document_state', 'value' => 'dynamic']) : '';
        is_null(seo('seo_home_global_index')) ? Seo::create(['key' => 'seo_home_global_index', 'value' => 0]) : '';
    }
}
