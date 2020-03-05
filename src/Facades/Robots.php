<?php

namespace SpaceCode\Maia\Facades;

use Illuminate\Support\Facades\Facade;

class Robots extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'robots';
    }
}