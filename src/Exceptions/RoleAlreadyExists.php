<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class RoleAlreadyExists extends InvalidArgumentException
{
    public static function create(string $name, string $guardName)
    {
        return new static(trans('maia::exeptions.role.alreadyexist.create', ['name' => $name, 'guardName' => $guardName]));
    }
}
