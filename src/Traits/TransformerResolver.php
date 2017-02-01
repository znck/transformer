<?php namespace Znck\Transformers\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Znck\Transformers\Transformer;

/**
 * Class TransformerResolver
 *
 * @internal Znck\Transformers
 */
trait TransformerResolver
{
    /**
     * List of registered transformers.
     *
     * @var array
     */
    public static $transformers = [];

    /**
     * List of model names.
     *
     * @var array
     */
    public static $relations;

    /**
     * Inverse of $relations.
     *
     * @var array
     */
    public static $invertedModels;

    /**
     * Register transformers.
     *
     * @param array $map
     */
    public static function register(array $map) {
        self::$transformers += $map;
    }

    /**
     * Bidirectional model type resolution.
     *
     * @param string $model
     *
     * @return string
     */
    public static function resolveModelType(string $model) {
        if (! self::$relations) {
            self::$relations = Relation::morphMap();
            self::$invertedModels = array_flip(self::$relations);
        }

        if (class_exists($model)) {
            return static::$invertedModels[$model] ?? $model;
        } else {
            return static::$relations[$model] ?? $model;
        }
    }

    /**
     * Resolve transformer.
     *
     * @param \Illuminate\Database\Eloquent\Model|string $model
     *
     * @return Transformer
     */
    static public function resolveTransformer($model) {
        if (!is_string($model)) {
            $model = get_class($model);
        }

        if (array_key_exists($model, Transformer::$transformers)) {
            return app(Transformer::$transformers[$model]);
        }

        $app = app();

        $name = str_replace($app->getNamespace(), '', $model);

        $dirs = ['Models', 'Eloquent'];

        if (str_contains($name, $dirs)) {
            foreach ($dirs as $dir) {
                $name = str_replace($dir, 'Transformers', $name);
            }
        } else {
            $name = 'Transformers\\'.$name;
        }

        $class = $app->getNamespace().$name.'Transformer';

        return $app->make($class);
    }
}