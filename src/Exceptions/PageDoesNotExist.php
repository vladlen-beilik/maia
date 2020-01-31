<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PageDoesNotExist extends InvalidArgumentException
{
    public static function sluged(string $pageSlug)
    {
        return new static("There is no page with slug `{$pageSlug}`.");
    }
    public static function named(string $pageTitle)
    {
        return new static("There is no page with this title `{$pageTitle}`.");
    }
    public static function withId(int $pageId)
    {
        return new static("There is no page with id `{$pageId}`.");
    }
}
