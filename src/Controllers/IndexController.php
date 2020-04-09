<?php

namespace SpaceCode\Maia\Controllers;

use App\Http\Controllers\Controller;
use SpaceCode\Maia\Models;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View as ViewModel;

class IndexController extends Controller
{
    /**
     * IndexController constructor.
     * @param $global
     */
    public function __construct($global = false)
    {
        if (!$global)
            $global = collect();
        $global->put('tracking_head', setting('tracking_google_tag_manager_head'));
        $global->put('tracking_body', setting('tracking_google_tag_manager_body'));
        $global->put('json_ld', null);
        if (isset($__env) && trim($__env->yieldContent('json_ld'))) {
            $global->put('json_ld', "<!-- Schema -->\n" . str_replace('&#039;', '"', html_entity_decode($__env->yieldContent('json_ld'), ENT_COMPAT, 'UTF-8')) . "\n");
        }
        $global->put('favicon', null);
        $siteFavicon = siteFavicon();
        if (!is_null($siteFavicon) && !empty($siteFavicon)) {
            $global->put('favicon', "    <!-- Favicon -->\n" . "    <link rel='icon' sizes='180x180' href='" . favicon(siteFavicon(), '180') . "' />\n" . "    <link rel='icon' sizes='32x32' href='" . favicon(siteFavicon(), '32') . "' />\n");
        }
        View::share('globalKey', (object)$global->toArray());
    }

    public function homeIndex()
    {
        $home = collect();
        $home->put('indexView', 'home');
        return (object)$home->toArray();
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function pageIndex($slug)
    {
        $item = Models\Page::whereSlug($slug)->where(['status' => 'published', 'deleted_at' => null])->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'page' : 'templates.pages.' . $item->template;
        return $item;
    }

    /**
     * @param $slugs
     * @return Factory|ViewModel
     */
    public function parentPageIndex($slugs)
    {
        $item = $slugs->reduce(function ($item, $slug) {
            return ($item->children()->where('slug', $slug)->firstOrFail());
        }, Models\Page::whereSlug($slugs->shift())->where(['status' => 'published', 'deleted_at' => null])->with('children')->firstOrFail());
        $item->indexView = $item->template === 'default' ? 'page' : 'templates.pages.' . $item->template;
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function postIndex($slug)
    {
        $item = Models\Post::whereSlug($slug)->where(['status' => 'published', 'deleted_at' => null])->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'post' : 'templates.posts.' . $item->template;
        getVariableVue($item);
        put_session_view('posts', $item);
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function postCategoryIndex($slug)
    {
        $item = Models\PostCategory::whereSlug($slug)->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'postCategory' : 'templates.postCategories.' . $item->template;
        return $item;
    }

    /**
     * @param $slugs
     * @return Factory|ViewModel
     */
    public function parentPostCategoryIndex($slugs)
    {
        $item = $slugs->reduce(function ($item, $slug) {
            return ($item->children()->where('slug', $slug)->firstOrFail());
        }, Models\PostCategory::whereSlug($slugs->shift())->with('children')->firstOrFail());
        $item->indexView = $item->template === 'default' ? 'postCategory' : 'templates.postCategories.' . $item->template;
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function postTagIndex($slug)
    {
        $item = Models\PostTag::whereSlug($slug)->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'postTag' : 'templates.postTags.' . $item->template;
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function portfolioIndex($slug)
    {
        $item = Models\Portfolio::whereSlug($slug)->where(['status' => 'published', 'deleted_at' => null])->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'portfolio' : 'templates.portfolio.' . $item->template;
        getVariableVue($item);
        put_session_view('portfolio', $item);
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function portfolioCategoryIndex($slug)
    {
        $item = Models\PortfolioCategory::whereSlug($slug)->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'portfolioCategory' : 'templates.portfolioCategories.' . $item->template;
        return $item;
    }

    /**
     * @param $slugs
     * @return Factory|ViewModel
     */
    public function parentPortfolioCategoryIndex($slugs)
    {
        $item = $slugs->reduce(function ($item, $slug) {
            return ($item->children()->where('slug', $slug)->firstOrFail());
        }, Models\PortfolioCategory::whereSlug($slugs->shift())->with('children')->firstOrFail());
        $item->indexView = $item->template === 'default' ? 'portfolioCategory' : 'templates.portfolioCategories.' . $item->template;
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function portfolioTagIndex($slug)
    {
        $item = Models\PortfolioTag::whereSlug($slug)->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'portfolioTag' : 'templates.portfolioTags.' . $item->template;
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function shopIndex($slug)
    {
        $item = Models\Shop::whereSlug($slug)->where(['status' => 'published', 'deleted_at' => null])->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'shop' : 'templates.shops.' . $item->template;
        put_session_view('shops', $item);
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function productIndex($slug)
    {
        $item = Models\Product::whereSlug($slug)->where(['status' => 'published', 'deleted_at' => null])->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'product' : 'templates.products.' . $item->template;
        getVariableVue($item);
        put_session_view('products', $item);
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function productCategoryIndex($slug)
    {
        $item = Models\ProductCategory::whereSlug($slug)->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'productCategory' : 'templates.productCategories.' . $item->template;
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function productBrandIndex($slug)
    {
        $item = Models\ProductBrand::whereSlug($slug)->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'productBrand' : 'templates.productBrands.' . $item->template;
        return $item;
    }

    /**
     * @param $slugs
     * @return Factory|ViewModel
     */
    public function parentProductCategoryIndex($slugs)
    {
        $item = $slugs->reduce(function ($item, $slug) {
            return ($item->children()->where('slug', $slug)->firstOrFail());
        }, Models\ProductCategory::whereSlug($slugs->shift())->with('children')->firstOrFail());
        $item->indexView = $item->template === 'default' ? 'productCategory' : 'templates.productCategories.' . $item->template;
        return $item;
    }

    /**
     * @param $slug
     * @return Factory|ViewModel
     */
    public function productTagIndex($slug)
    {
        $item = Models\ProductTag::whereSlug($slug)->firstOrFail();
        $item->indexView = $item->template === 'default' ? 'productTag' : 'templates.productTags.' . $item->template;
        return $item;
    }
}
