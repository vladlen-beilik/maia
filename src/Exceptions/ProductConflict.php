<?php

namespace SpaceCode\Maia\Exceptions;

use InvalidArgumentException;

class ProductConflict extends InvalidArgumentException
{
    public static function price()
    {
        return new static(trans('maia::exeptions.product.price'));
    }

    public static function discountPrice()
    {
        return new static(trans('maia::exeptions.product.discountPrice'));
    }

    public static function dateFromWithNow()
    {
        return new static(trans('maia::exeptions.product.dateFromWithNow'));
    }

    public static function dateToWithNow()
    {
        return new static(trans('maia::exeptions.product.dateToWithNow'));
    }

    public static function dateFromAddMinutes()
    {
        return new static(trans('maia::exeptions.product.dateFromAddMinutes'));
    }

    public static function dateFromWithTo()
    {
        return new static(trans('maia::exeptions.product.dateFromWithTo'));
    }

    public static function wholesale_price()
    {
        return new static(trans('maia::exeptions.product.wholesale_price'));
    }

    public static function discountWholesale_price()
    {
        return new static(trans('maia::exeptions.product.discountWholesale_price'));
    }

    public static function wholesale_dateFromWithNow()
    {
        return new static(trans('maia::exeptions.product.wholesale_dateFromWithNow'));
    }

    public static function wholesale_dateToWithNow()
    {
        return new static(trans('maia::exeptions.product.wholesale_dateToWithNow'));
    }

    public static function wholesale_dateFromAddMinutes()
    {
        return new static(trans('maia::exeptions.product.wholesale_dateFromAddMinutes'));
    }

    public static function wholesale_dateFromWithTo()
    {
        return new static(trans('maia::exeptions.product.wholesale_dateFromWithTo'));
    }
}
