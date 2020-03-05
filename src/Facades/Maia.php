<?php

namespace SpaceCode\Maia\Facades;

use Illuminate\Support\Facades\Facade;

class Maia extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'maia';
    }
}
