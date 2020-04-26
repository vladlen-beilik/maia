<?php

namespace SpaceCode\Maia\Javascript;

interface ViewBinder
{
    /**
     * Bind the JavaScript variables to the view.
     *
     * @param string $js
     */
    public function bind($js);
}