<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PortfolioTagDoesNotExist extends InvalidArgumentException
{
    public static function sluged(string $slug)
    {
        return new static(trans('maia::exeptions.portfolioTag.doesnotexist.sluged', ['slug' => $slug]));
    }
    public static function named(string $title)
    {
        return new static(trans('maia::exeptions.portfolioTag.doesnotexist.named', ['title' => $title]));
    }
    public static function withId(int $id)
    {
        return new static(trans('maia::exeptions.portfolioTag.doesnotexist.withId', ['id' => $id]));
    }
}
