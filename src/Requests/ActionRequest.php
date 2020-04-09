<?php

namespace SpaceCode\Maia\Requests;

use SpaceCode\Maia\Traits\HasDependencies;
use Laravel\Nova\Http\Requests\ActionRequest as NovaActionRequest;

class ActionRequest extends NovaActionRequest {

    use HasDependencies;
}