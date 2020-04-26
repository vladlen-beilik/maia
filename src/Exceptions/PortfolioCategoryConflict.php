<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PortfolioCategoryConflict extends InvalidArgumentException
{
    public static function url(string $slug, string $guardName)
    {
        return new static(_trans('maia::exeptions.portfolioCategory.url', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
