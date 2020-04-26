<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;
use Illuminate\Support\Collection;

class GuardDoesNotMatch extends InvalidArgumentException
{
    public static function create(string $givenGuard, Collection $expectedGuards)
    {
        return new static(_trans('maia::exeptions.guard', ['expected' => $expectedGuards->implode(', '), 'given' => $givenGuard]));
    }
}
