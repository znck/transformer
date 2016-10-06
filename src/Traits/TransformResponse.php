<?php namespace Znck\Transformers\Traits;

use Znck\Transformers\Traits\TransformerManager;

trait TransformResponse
{
    public function callAction($method, $parameters) {
        $data = parent::callAction($method, $parameters);

        return TransformerManager::response($data);
    }
}
