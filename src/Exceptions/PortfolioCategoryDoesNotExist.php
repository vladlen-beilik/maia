<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PortfolioCategoryDoesNotExist extends InvalidArgumentException
{
    public static function sluged(string $slug)
    {
        return new static(trans('maia::exeptions.portfolioCategory.doesnotexist.sluged', ['slug' => $slug]));
    }
    public static function named(string $title)
    {
        return new static(trans('maia::exeptions.portfolioCategory.doesnotexist.named', ['title' => $title]));
    }
    public static function withId(int $id)
    {
        return new static(trans('maia::exeptions.portfolioCategory.doesnotexist.withId', ['id' => $id]));
    }
}
