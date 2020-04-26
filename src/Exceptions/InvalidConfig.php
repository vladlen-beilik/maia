<?php

namespace SpaceCode\Maia\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function driverNotSupported()
    {
        return new static(_trans('maia::exeptions.drivernotsupported'));
    }
}
