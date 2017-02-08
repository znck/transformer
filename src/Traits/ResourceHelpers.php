<?php

namespace Znck\Transformers\Traits;

use Znck\Transformers\Transformer;


/**
 * Class IntelligentHelpers
 *
 * @internal Znck\Transformers
 *
 * @method null null() A null resource.
 */
trait ResourceHelpers
{
    /**
     * Transform single item resource.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     * @param \Znck\Transformers\Transformer|null $transformer
     * @param string|null $resourceKey
     *
     * @return mixed
     */
    protected function item($item, $transformer = null, $resourceKey = null) {
        if (!$item) {
            return $this->null();
        }

        $transformer = $transformer ?? $this->getTransformer($item)->setIndexing(false);

        return parent::item($item, $transformer, $resourceKey);
    }

    /**
     * Transform a collection resource.
     *
     * @param \Illuminate\Database\Eloquent\Collection|array $items
     * @param \Znck\Transformers\Transformer|null $transformer
     * @param string|null $resourceKey
     *
     * @return mixed
     */
    protected function collection($items, $transformer = null, $resourceKey = null) {
        if (!$items) {
            $items = [];
        }

        $item = collect($items)->first();
        $transformer = $transformer ?? $this->getTransformer($item)->setIndexing();

        return parent::collection($items, $transformer, $resourceKey);
    }

    /**
     * Find transformer for item.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     *
     * @return \Znck\Transformers\Transformer
     */
    private function getTransformer($item) {
        return transformer($item);
    }
}
