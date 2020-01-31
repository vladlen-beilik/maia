<?php

namespace SpaceCode\Maia;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaTool extends Tool
{
    public $roleResource = Role::class;
    public $permissionResource = Permission::class;
    public $pageResource = Page::class;

    public function boot()
    {
        Nova::resources([
            $this->roleResource,
            $this->permissionResource,
            $this->pageResource,
        ]);
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
        $this->permissionResource = $permissionResource;
        return $this;
    }
}
