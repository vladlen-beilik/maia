<?php

namespace SpaceCode\Maia\Javascript\Transformers;

class DefaultTransformer
{
    public function transform($value)
    {
        return json_encode($value);
    }
}