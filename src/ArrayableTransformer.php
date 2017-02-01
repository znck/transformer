<?php namespace Znck\Transformers;

use Illuminate\Contracts\Support\Arrayable;

class ArrayableTransformer extends Transformer
{
    /**
     * @param \Illuminate\Contracts\Support\Arrayable|array|mixed $model
     *
     * @return array
     */
    public function transform($model) {
        if ($model instanceof Arrayable) {
            return $model->toArray();
        }

        return  (array) $model;
    }
}
