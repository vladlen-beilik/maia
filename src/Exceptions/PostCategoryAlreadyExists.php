<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PostCategoryAlreadyExists extends InvalidArgumentException
{
    public static function create(string $slug, string $guardName)
    {
        return new static(trans('maia::exeptions.postCategory.alreadyexist.create', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
