<?php

namespace SpaceCode\Maia\Controllers;

use Illuminate\Routing\Controller;

class HorizonController extends Controller
{
    public function __invoke() : string
    {
        return config('horizon.path');
    }
}