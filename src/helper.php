<?php

use Znck\Transformers\Transformer;

if (!function_exists('transformer')) {
    /**
     * Find a transformer.
     *
     * @param mixed $item
     *
     * @return Transformer
     */
    function transformer($item)
    {
        return Transformer::transformer($item);
    }
}

if (!function_exists('transform')) {
    /**
     * @param mixed|\Illuminate\Database\Eloquent\Model $item
     * @param null|array $includes
     * @param null|array $excludes
     * @param bool $guess
     *
     * @return array
     */
    function transform($item, $includes = null, $excludes = null, $guess = false)
    {
        return Transformer::response($item, $includes, $excludes, $guess);
    }
}
