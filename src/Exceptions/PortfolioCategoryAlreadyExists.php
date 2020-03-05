<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PortfolioCategoryAlreadyExists extends InvalidArgumentException
{
    public static function create(string $slug, string $guardName)
    {
        return new static(trans('maia::exeptions.portfolioCategory.alreadyexist.create', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
