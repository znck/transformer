<?php namespace Znck\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use League\Fractal\TransformerAbstract;
use Znck\Transformers\Traits\IntelligentHelpers;
use Znck\Transformers\Traits\TransformerManager;
use Znck\Transformers\Traits\TransformerResolver;

abstract class Transformer extends TransformerAbstract
{
    use TransformerManager, IntelligentHelpers, TransformerResolver;

    protected $indexing = false;

    protected $timestamps = true;

    public function transformId(Model $model) {
        return [
            'id' => $model->getKey(),
            '_type' => self::resolveModelType(get_class($model)),
        ];
    }

    public function transformTimestamps(Model $model) {
        if ($this->timestamps and $model->usesTimestamps()) {
            $created = $model->created_at;
            $updated = $model->updated_at;

            return [
                'created_at' => $created ? $created->toIso8601String() : '',
                'updated_at' => $updated ? $updated->toIso8601String() : '',
            ];
        }

        return [];
    }

    public function transform($model) {
        $method = $this->isIndexing() ? 'index' : 'show';

        return $this->transformId($model)
               + $this->transformTimestamps($model)
               + $this->$method($model);
    }

    /**
     * Set indexing!
     *
     * @param boolean $indexing
     *
     * @return Transformer
     */
    public function setIndexing(bool $indexing = null): Transformer {
        $this->indexing = is_bool($indexing) ? $indexing : true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIndexing(): bool {
        return $this->indexing;
    }
}
