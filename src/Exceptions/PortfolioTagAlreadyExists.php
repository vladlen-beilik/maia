<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PortfolioTagAlreadyExists extends InvalidArgumentException
{
    public static function create(string $slug, string $guardName)
    {
        return new static(trans('maia::exeptions.portfolioTag.alreadyexist.create', ['slug' => $slug, 'guardName' => $guardName]));
    }
}
