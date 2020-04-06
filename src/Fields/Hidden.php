<?php

namespace SpaceCode\Maia\Fields;

use Laravel\Nova\Fields\Field;

class Hidden extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'hidden-field';

    /**
     * Fill the field value with the provided value.
     *
     * @param  string $value
     * @return $this
     */
    public function default(string $value = null)
    {
        return $this->withMeta(['value' => $value]);
    }
}