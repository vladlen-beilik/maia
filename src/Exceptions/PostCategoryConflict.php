<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PostCategoryConflict extends InvalidArgumentException
{
    public static function url(string $slug, string $guardName)
    {
        return new static(_trans('maia::exeptions.postCategory.url', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
