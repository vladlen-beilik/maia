<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PermissionAlreadyExists extends InvalidArgumentException
{
    public static function create(string $name, string $guardName)
    {
        return new static(_trans('maia::exeptions.permission.alreadyexist.create', ['name' => $name, 'guardName' => $guardName]));
    }
}
