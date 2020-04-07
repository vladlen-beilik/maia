<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Fields;

use Laravel\Nova\Element;
use Laravel\Nova\Fields\Text;

class SluggableText extends Text
{
    public $component = 'sluggabletext-field';

    /**
     * Specify the field that contains the actual slug.
     *
     * @param string $slugField
     *
     * @return $this
     */
    public function slug($slugField = 'slug'): Element
    {
        return $this->withMeta([__FUNCTION__ => $slugField]);
    }
}