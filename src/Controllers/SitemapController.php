<?php

namespace SpaceCode\Maia\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use SpaceCode\Maia\Models;

class SitemapController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $sitemap = App::make('sitemap');
        if(siteIndex()) {
            $homemod = File::exists(resource_path('views/home.blade.php')) ? date('c', File::lastModified(resource_path('views/home.blade.php'))) : '';
            $sitemap->addSitemap(URL::to('sitemap-homepage.xml'), $homemod);

            $pages = Models\Page::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
            if($pages->count() > 0)
                $sitemap->addSitemap(URL::to('sitemap-pages.xml'), $this->formatC($pages));

            if(isBlog()) {

                $posts = Models\Post::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
                if($posts->count() > 0)
                    $sitemap->addSitemap(URL::to('sitemap-posts.xml'), $this->formatC($posts));

                if(seo('seo_post_categories_show_index')) {
                    $postCategories = Models\PostCategory::where(['guard_name' => 'web']);
                    if($postCategories->count() > 0)
                        $sitemap->addSitemap(URL::to('sitemap-post-categories.xml'), $this->formatC($postCategories));
                }
                if(seo('seo_post_tags_show_index')) {
                    $postTags = Models\PostTag::where(['guard_name' => 'web']);
                    if($postTags->count() > 0)
                        $sitemap->addSitemap(URL::to('sitemap-post-tags.xml'), $this->formatC($postTags));
                }

            }
            if(isPortfolio()) {

                $portfolio = Models\Portfolio::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
                if($portfolio->count() > 0)
                    $sitemap->addSitemap(URL::to('sitemap-portfolio.xml'), $this->formatC($portfolio));

                if(seo('seo_portfolio_categories_show_index')) {
                    $portfolioCategories = Models\PortfolioCategory::where(['guard_name' => 'web']);
                    if($portfolioCategories->count() > 0)
                        $sitemap->addSitemap(URL::to('sitemap-portfolio-categories.xml'), $this->formatC($portfolioCategories));
                }
                if(seo('seo_portfolio_tags_show_index')) {
                    $portfolioTags = Models\PortfolioTag::where(['guard_name' => 'web']);
                    if($portfolioTags->count() > 0)
                        $sitemap->addSitemap(URL::to('sitemap-portfolio-tags.xml'), $this->formatC($portfolioTags));
                }

            }
            if(isShop() && isActiveShop()) {

                $shops = Models\Shop::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
                if($shops->count() > 0)
                    $sitemap->addSitemap(URL::to('sitemap-shops.xml'), $this->formatC($shops));

                $products = Models\Product::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
                if($products->count() > 0)
                    $sitemap->addSitemap(URL::to('sitemap-products.xml'), $this->formatC($products));

                if(seo('seo_product_categories_show_index')) {
                    $productCategories = Models\ProductCategory::where(['guard_name' => 'web']);
                    if($productCategories->count() > 0)
                        $sitemap->addSitemap(URL::to('sitemap-product-categories.xml'), $this->formatC($productCategories));
                }
                if(seo('seo_product_brands_show_index')) {
                    $productBrands = Models\ProductBrand::where(['guard_name' => 'web']);
                    if($productBrands->count() > 0)
                        $sitemap->addSitemap(URL::to('sitemap-product-brands.xml'), $this->formatC($productBrands));
                }
                if(seo('seo_product_tags_show_index')) {
                    $productTags = Models\ProductTag::where(['guard_name' => 'web']);
                    if($productTags->count() > 0)
                        $sitemap->addSitemap(URL::to('sitemap-product-tags.xml'), $this->formatC($productTags));
                }

            }
        }
        return $sitemap;
    }

    public function home() {
        $sitemap = App::make('sitemap');
        $sitemap->add(URL::to(''), date('c', File::lastModified(resource_path('views/home.blade.php'))), '1.0', 'daily');
        return $sitemap->render('xml');
    }

    public function pages() {
        $items = Models\Page::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.9', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function posts() {
        $items = Models\Post::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.9', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function postCategories() {
        $items = Models\PostCategory::where(['guard_name' => 'web']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.8', 'daily');
            }
        }
        return $sitemap->render('xml');

    }

    public function postTags() {
        $items = Models\PostTag::where(['guard_name' => 'web']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.8', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function portfolio() {
        $items = Models\Portfolio::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.9', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function portfolioCategories() {
        $items = Models\PortfolioCategory::where(['guard_name' => 'web']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.8', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function portfolioTags() {
        $items = Models\PortfolioTag::where(['guard_name' => 'web']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.8', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function shops() {
        $items = Models\Shop::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '1.0', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function products() {
        $items = Models\Product::where(['guard_name' => 'web', 'deleted_at' => null, 'status' => 'published']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.9', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function productCategories() {
        $items = Models\ProductCategory::where(['guard_name' => 'web']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.8', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function productBrands() {
        $items = Models\ProductBrand::where(['guard_name' => 'web']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.8', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function productTags() {
        $items = Models\ProductTag::where(['guard_name' => 'web']);
        $sitemap = App::make('sitemap');
        if($items->count() > 0) {
            foreach ($items->orderBy('created_at', 'desc')->get() as $item) {
                $sitemap->add(URL::to($item->getUrl()), $item->updated_at->format('c'), '0.8', 'daily');
            }
        }
        return $sitemap->render('xml');
    }

    public function formatC($eom) {
        return $eom->orderBy('updated_at', 'desc')->first()->updated_at->format('c');
    }
}