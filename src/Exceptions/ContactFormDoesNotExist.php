<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class ContactFormDoesNotExist extends InvalidArgumentException
{
    public static function named(string $title)
    {
        return new static(trans('maia::exeptions.contactForm.doesnotexist.named', ['title' => $title]));
    }
    public static function withId(int $id)
    {
        return new static(trans('maia::exeptions.contactForm.doesnotexist.withId', ['id' => $id]));
    }
}
