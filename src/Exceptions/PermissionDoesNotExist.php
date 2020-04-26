<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PermissionDoesNotExist extends InvalidArgumentException
{
    public static function create(string $name, string $guardName = '')
    {
        return new static(_trans('maia::exeptions.permission.doesnotexist.create', ['name' => $name, 'guardName' => $guardName]));
    }
    public static function withId(int $id)
    {
        return new static(_trans('maia::exeptions.permission.doesnotexist.withId', ['id' => $id]));
    }
}
