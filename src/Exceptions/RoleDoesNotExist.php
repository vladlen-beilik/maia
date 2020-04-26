<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class RoleDoesNotExist extends InvalidArgumentException
{
    public static function named(string $name)
    {
        return new static(_trans('maia::exeptions.role.doesnotexist.named', ['name' => $name]));
    }
    public static function withId(int $id)
    {
        return new static(_trans('maia::exeptions.role.doesnotexist.withId', ['id' => $id]));
    }
}
