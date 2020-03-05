<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PortfolioAlreadyExists extends InvalidArgumentException
{
    public static function create(string $slug, string $guardName)
    {
        return new static(trans('maia::exeptions.portfolio.alreadyexist.create', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
