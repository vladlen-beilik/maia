<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PageConflict extends InvalidArgumentException
{
    public static function url(string $slug, string $guardName)
    {
        return new static(_trans('maia::exeptions.page.url', ['slug' => $slug, 'guardName' => $guardName]));
    }

    public static function ban(string $slug)
    {
        return new static(_trans('maia::exeptions.page.ban', ['slug' => $slug]));
    }

    public static function reserved(string $slug)
    {
        return new static(_trans('maia::exeptions.page.reserved', ['slug' => $slug]));
    }
}
