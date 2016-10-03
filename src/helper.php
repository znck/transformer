<?php namespace {

    use Znck\Transformers\Factory;
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
            return Factory::transformer($item);
        }
    }
}
