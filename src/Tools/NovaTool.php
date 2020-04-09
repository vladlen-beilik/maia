<?php

namespace SpaceCode\Maia\Tools;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use SpaceCode\Maia\Resources;

class NovaTool extends Tool
{
    public $roleResource = Resources\Role::class;
    public $permissionResource = Resources\Permission::class;
    public $pageResource = Resources\Page::class;
    public $commentResource = Resources\Comment::class;
    public $postResource = Resources\Post::class;
    public $postCategoryResource = Resources\PostCategory::class;
    public $postTagResource = Resources\PostTag::class;
    public $portfolioResource = Resources\Portfolio::class;
    public $portfolioCategoryResource = Resources\PortfolioCategory::class;
    public $portfolioTagResource = Resources\PortfolioTag::class;
    public $shopResource = Resources\Shop::class;
    public $productResource = Resources\Product::class;
    public $contactFormResource = Resources\ContactForm::class;

    public function boot()
    {
        if(!isBlog()) {
            $this->postResource = null;
            $this->postCategoryResource = null;
            $this->postTagResource = null;
        }
        if(!isPortfolio()) {
            $this->portfolioResource = null;
            $this->portfolioCategoryResource = null;
            $this->portfolioTagResource = null;
        }
        if(!isShop()) {
            $this->shopResource = null;
            if(!isActiveShop()) {
                $this->productResource = null;
            }
        }
        if(!isBlog() && !isPortfolio() && !isShop()) {
            $this->commentResource = null;
        }
        Nova::resources(array_filter([
            $this->roleResource,
            $this->permissionResource,
            $this->pageResource,
            $this->commentResource,
            $this->postResource,
            $this->postCategoryResource,
            $this->postTagResource,
            $this->portfolioResource,
            $this->portfolioCategoryResource,
            $this->portfolioTagResource,
            $this->shopResource,
            $this->productResource,
            $this->contactFormResource,
        ]));
    }

    /**
     * @param string $roleResource
     * @return $this
     */
    public function roleResource(string $roleResource)
    {
        $this->roleResource = $roleResource;
        return $this;
    }

    /**
     * @param string $permissionResource
     * @return $this
     */
    public function permissionResource(string $permissionResource)
    {
        $this->permissionResource = $permissionResource;
        return $this;
    }

    /**
     * @param string $pageResource
     * @return $this
     */
    public function pageResource(string $pageResource)
    {
        $this->pageResource = $pageResource;
        return $this;
    }

    /**
     * @param string $commentResource
     * @return $this
     */
    public function commentResource(string $commentResource)
    {
        $this->commentResource = $commentResource;
        return $this;
    }

    /**
     * @param string $postResource
     * @return $this
     */
    public function postResource(string $postResource)
    {
        $this->postResource = $postResource;
        return $this;
    }

    /**
     * @param string $postCategoryResource
     * @return $this
     */
    public function postCategoryResource(string $postCategoryResource)
    {
        $this->postCategoryResource = $postCategoryResource;
        return $this;
    }

    /**
     * @param string $postTagResource
     * @return $this
     */
    public function postTagResource(string $postTagResource)
    {
        $this->postTagResource = $postTagResource;
        return $this;
    }

    /**
     * @param string $portfolioResource
     * @return $this
     */
    public function portfolioResource(string $portfolioResource)
    {
        $this->portfolioResource = $portfolioResource;
        return $this;
    }

    /**
     * @param string $portfolioCategoryResource
     * @return $this
     */
    public function portfolioCategoryResource(string $portfolioCategoryResource)
    {
        $this->portfolioCategoryResource = $portfolioCategoryResource;
        return $this;
    }

    /**
     * @param string $portfolioTagResource
     * @return $this
     */
    public function portfolioTagResource(string $portfolioTagResource)
    {
        $this->portfolioTagResource = $portfolioTagResource;
        return $this;
    }

    /**
     * @param string $shopResource
     * @return $this
     */
    public function shopResource(string $shopResource)
    {
        $this->shopResource = $shopResource;
        return $this;
    }


    /**
     * @param string $productResource
     * @return $this
     */
    public function productResource(string $productResource)
    {
        $this->productResource = $productResource;
        return $this;
    }

    /**
     * @param string $contactFormResource
     * @return $this
     */
    public function contactFormResource(string $contactFormResource)
    {
        $this->contactFormResource = $contactFormResource;
        return $this;
    }
}
