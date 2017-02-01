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
trait IntelligentHelpers
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

        $transformer = $transformer ?? $this->getTransformer($item);
        $resourceKey = $resourceKey ?? $this->getResourceKey($item);

        if ($transformer instanceof Transformer) {
            $transformer->setIndexing(false);
        }

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
            return $this->null();
        }

        $item = collect($items)->first();
        $transformer = $transformer ?? $this->getTransformer($item);
        $resourceKey = $resourceKey ?? str_plural($this->getResourceKey($item));

        if ($transformer instanceof Transformer) {
            $transformer->setIndexing();
        }

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

    /**
     * Create resource name for item.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     *
     * @return string
     */
    private function getResourceKey($item):string {
        if (is_object($item)) {
            return strtolower(class_basename(get_class($item)));
        }

        return 'item';
    }
}
