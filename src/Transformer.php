<?php namespace Znck\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use League\Fractal\TransformerAbstract;
use Znck\Transformers\Traits\TransformerManager;

abstract class Transformer extends TransformerAbstract
{
    use TransformerManager;

    public static $transformers = [];

    protected static $relations;

    protected static $invertedModels;

    protected $indexing = false;

    public static function register(array $map) {
        self::$transformers += $map;
    }

    public static function resolveModelType(string $model) {
        if (!self::$relations) {
            self::$relations = Relation::morphMap();
            self::$invertedModels = array_flip(self::$relations);
        }

        if (class_exists($model)) {
            return static::$invertedModels[$model] ?? $model;
        } else {
            return static::$relations[$model] ?? $model;
        }
    }

    public function transformId(Model $model) {
        return [
            'id' => $model->getKey(),
            '_type' => self::resolveModelType(get_class($model)),
        ];
    }

    public function transformTimestamps(Model $model) {
        if ($model->usesTimestamps()) {
            /** @var \Carbon\Carbon $created */
            $created = $model->created_at;
            /** @var \Carbon\Carbon $updated */
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
    public function setIndexing(bool $indexing = true): Transformer {
        $this->indexing = $indexing;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIndexing(): bool {
        return $this->indexing;
    }
}
