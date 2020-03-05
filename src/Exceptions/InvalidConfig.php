<?php

namespace SpaceCode\Maia\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function driverNotSupported()
    {
        return new static(trans('maia::exeptions.drivernotsupported'));
    }
}
