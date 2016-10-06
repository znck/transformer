<?php namespace {

    use Znck\Transformers\Traits\TransformerManager;
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
}
