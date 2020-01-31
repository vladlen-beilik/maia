<?php

namespace SpaceCode\Maia;

use Laravel\Nova\Element;
use Laravel\Nova\Fields\Text;

class SluggableText extends Text
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'maia-sluggable-sluggabletext-field';

    /**
     * @param string $slugField
     * @return Element
     */
    public function slug($slugField = 'Slug'): Element
    {
        return $this->withMeta([__FUNCTION__ => $slugField]);
    }
}
