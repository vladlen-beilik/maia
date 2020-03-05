<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PostAlreadyExists extends InvalidArgumentException
{
    public static function create(string $slug, string $guardName)
    {
        return new static(trans('maia::exeptions.post.alreadyexist.create', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
