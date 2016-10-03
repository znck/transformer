<?php namespace Znck\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use League\Fractal\TransformerAbstract;

abstract class Transformer extends TransformerAbstract
{
    public static $transformers = [];

    protected static $relations;

    protected static $invertedModels;

    public static function register(array $map) {
        self::$transformers += $map;
    }

    public static function resolveModelType(string $model) {
        if (!self::$relations) {
            self::$relations = Relation::morphMap();
            self::$invertedModels = array_flip(self::$relations);
        }

        if (class_exists($model)) {
            return static::$relations[$model] ?? $model;
        } else {
            return static::$invertedModels[$model] ?? $model;
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
        return $this->transformId($model)
               + $this->transformTimestamps($model)
               + $this->handle($model);
    }
}
