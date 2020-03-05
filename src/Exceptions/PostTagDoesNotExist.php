<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class PostTagDoesNotExist extends InvalidArgumentException
{
    public static function sluged(string $slug)
    {
        return new static(trans('maia::exeptions.postTag.doesnotexist.sluged', ['slug' => $slug]));
    }
    public static function named(string $title)
    {
        return new static(trans('maia::exeptions.postTag.doesnotexist.named', ['title' => $title]));
    }
    public static function withId(int $id)
    {
        return new static(trans('maia::exeptions.postTag.doesnotexist.withId', ['id' => $id]));
    }
}
