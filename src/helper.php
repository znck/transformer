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
    function transformer($item) {
        return Transformer::transformer($item);
    }
}

if (!function_exists('transform')) {
    /**
     * @param mixed|\Illuminate\Database\Eloquent\Model $item
     *
     * @return array
     */
    function transform($item, $includes = null, $excludes = null) {
        return Transformer::response($item, $includes, $excludes);
    }
}
