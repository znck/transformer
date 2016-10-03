<?php namespace Znck\Transformers\Traits;

use Znck\Transformers\Factory;

trait TransformResponse
{
    public function callAction($method, $parameters) {
        $data = parent::callAction($method, $parameters);

        return Factory::response($data);
    }
}
