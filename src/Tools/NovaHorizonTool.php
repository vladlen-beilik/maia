<?php

namespace SpaceCode\Maia\Tools;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaHorizonTool extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('maia-horizon', __DIR__ . '/../../dist/js/horizon.js');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('maia-horizon::navigation');
    }
}