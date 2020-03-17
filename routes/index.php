<?php

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

if (
    !\Request::is('admin') &&
    !\Request::is('admin/*') &&
    !\Request::is('nova-api') &&
    !\Request::is('nova-api/*') &&
    !\Request::is('nova-vendor') &&
    !\Request::is('nova-vendor/*')
) {
    Route::group(['as' => 'maia.'], function () {
        Route::group(['prefix' => '/'], function () {
            Route::get('robots.txt', ['uses' => 'MaiaRobotsController', 'as' => 'robot']);
            Route::get('sitemap.xml', ['uses' => 'MaiaSitemapController@index', 'as' => 'sitemap-xml']);
            Route::get('sitemap-homepage.xml', ['uses' => 'MaiaSitemapController@home', 'as' => 'sitemap-homepage-xml']);
            Route::get('sitemap-pages.xml', ['uses' => 'MaiaSitemapController@pages', 'as' => 'sitemap-pages-xml']);
            if(isBlog()) {
                Route::get('sitemap-posts.xml', ['uses' => 'MaiaSitemapController@posts', 'as' => 'sitemap-posts-xml']);
                Route::get('sitemap-post-categories.xml', ['uses' => 'MaiaSitemapController@postCategories', 'as' => 'sitemap-post-categories-xml']);
                Route::get('sitemap-post-tags.xml', ['uses' => 'MaiaSitemapController@postTags', 'as' => 'sitemap-post-tags-xml']);
            }
            if(isPortfolio()) {
                Route::get('sitemap-portfolio.xml', ['uses' => 'MaiaSitemapController@portfolio', 'as' => 'sitemap-portfolio-xml']);
                Route::get('sitemap-portfolio-categories.xml', ['uses' => 'MaiaSitemapController@portfolioCategories', 'as' => 'sitemap-portfolio-categories-xml']);
                Route::get('sitemap-portfolio-tags.xml', ['uses' => 'MaiaSitemapController@portfolioTags', 'as' => 'sitemap-portfolio-tags-xml']);
            }
            Route::get('/', ['uses' => 'MaiaIndexController@homeIndex', 'as' => 'home']);
            Route::get('{slug}/', ['uses' => 'MaiaIndexController@pageIndex', 'as' => 'page']);
            Route::get('{slug}/{slug2?}/{slug3?}/{slug4?}/{slug5?}/{slug6?}/{slug7?}/{slug8?}/{slug9?}/{slug10?}', ['uses' => 'MaiaIndexController@parentPageIndex', 'as' => 'parent-page']);
            if(isBlog()) {
                Route::get(seo('seo_posts_prefix') . '/{slug}/', ['uses' => 'MaiaIndexController@postIndex', 'as' => 'post']);
                Route::get(seo('seo_post_tags_prefix') . '/{slug}/', ['uses' => 'MaiaIndexController@postTagIndex', 'as' => 'tag']);
                Route::get(seo('seo_post_categories_prefix') . '{slug}/{slug2?}/{slug3?}/{slug4?}/{slug5?}/{slug6?}/{slug7?}/{slug8?}/{slug9?}/{slug10?}', ['uses' => 'MaiaIndexController@parentPostCategoryIndex', 'as' => 'parent-category']);
            }
            if(isPortfolio()) {
                Route::get(seo('seo_portfolio_prefix') . '/{slug}/', ['uses' => 'MaiaIndexController@portfolioIndex', 'as' => 'portfolio']);
                Route::get(seo('seo_portfolio_tags_prefix') . '/{slug}/', ['uses' => 'MaiaIndexController@portfolioTagIndex', 'as' => 'portfolio-tag']);
                Route::get(seo('seo_portfolio_categories_prefix') . '{slug}/{slug2?}/{slug3?}/{slug4?}/{slug5?}/{slug6?}/{slug7?}/{slug8?}/{slug9?}/{slug10?}', ['uses' => 'MaiaIndexController@parentPortfolioCategoryIndex', 'as' => 'parent-portfolio-category']);
            }
        });
    });
}
