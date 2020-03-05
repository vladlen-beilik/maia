<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PostTagAlreadyExists extends InvalidArgumentException
{
    public static function create(string $slug, string $guardName)
    {
        return new static(trans('maia::exeptions.postTag.alreadyexist.create', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
