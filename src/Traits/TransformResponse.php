<?php namespace Znck\Transformers\Traits;

use Znck\Transformers\Transformer;

trait TransformResponse
{
    public function callAction($method, $parameters) {
        return Transformer::response(parent::callAction($method, $parameters));
    }
}
