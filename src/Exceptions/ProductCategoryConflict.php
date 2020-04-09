<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class ProductCategoryConflict extends InvalidArgumentException
{
    public static function url(string $slug, string $guardName)
    {
        return new static(trans('maia::exeptions.productCategory.url', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
