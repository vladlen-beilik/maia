<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PostCategoryDoesNotExist extends InvalidArgumentException
{
    public static function sluged(string $slug)
    {
        return new static(trans('maia::exeptions.postCategory.doesnotexist.sluged', ['slug' => $slug]));
    }
    public static function named(string $title)
    {
        return new static(trans('maia::exeptions.postCategory.doesnotexist.named', ['title' => $title]));
    }
    public static function withId(int $id)
    {
        return new static(trans('maia::exeptions.postCategory.doesnotexist.withId', ['id' => $id]));
    }
}
