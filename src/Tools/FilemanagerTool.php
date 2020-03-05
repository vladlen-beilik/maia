<?php

namespace SpaceCode\Maia\Tools;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;

class FilemanagerTool extends BaseTool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('maia-filemanager', __DIR__.'/../../dist/js/filemanager-tool.js');
    }
    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('maia-filemanager::navigation');
    }
}
