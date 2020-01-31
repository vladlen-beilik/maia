<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PageAlreadyExists extends InvalidArgumentException
{
    public static function create(string $pageSlug, string $guardName)
    {
        return new static("This page `{$pageSlug}` is already exists for guard `{$guardName}`.");
    }
}
