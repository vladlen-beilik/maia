<?php

namespace SpaceCode\Maia\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function driverNotSupported()
    {
        return new static('Driver not supported. Please check your configuration');
    }
}
