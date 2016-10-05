<?php namespace Znck\Transformers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorWrapper;
use Illuminate\Pagination\Paginator as LaravelPaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as Items;
use League\Fractal\Resource\Item;


class Factory
{
    static public function response($data) {
        $manager = new Manager();

        if ($data instanceof Collection) {
            $resource = new Items($data->all(), self::setIndexing(self::transformer($data)));
        } elseif ($data instanceof Paginator) {
            $resource = new Items($data->items(), self::setIndexing(self::transformer($data)));
            if ($data instanceof LengthAwarePaginator) {
                $resource->setPaginator(new IlluminatePaginatorAdapter($data));
            } else {
                $before = $data->perPage() * ($data->currentPage() - 1);
                $after = $data->perPage() === count($data->items()) ? 1 : 0;
                $wrapped = new PaginatorWrapper(
                    $data->items(),
                    $before + count($data->items()) + $after,
                    $data->perPage(),
                    $data->currentPage(),
                    [
                        'path' => LaravelPaginator::resolveCurrentPath(),
                        'pageName' => $data->pageName ?? 'page',
                    ]
                );
                $resource->setPaginator(new IlluminatePaginatorAdapter($wrapped));
            }
        } elseif ($data instanceof Model) {
            $resource = new Item($data, self::transformer($data));
        }

        if (!isset($resource)) {
            return $data;
        }

        return $manager
            ->parseIncludes('with')
            ->parseExcludes('without')
            ->createData($resource)->toArray();
    }

    static public function resolveTransformer($model) {
        if (!is_string($model)) {
            $model = get_class($model);
        }

        if (array_key_exists($model, Transformer::$transformers)) {
            return app(Transformer::$transformers[$model]);
        }

        $name = str_replace(app()->getNamespace(), '', $model);

        $dirs = ['Models', 'Eloquent'];
        if (str_contains($name, $dirs)) {
            foreach ($dirs as $dir) {
                $name = str_replace($dir, 'Transformers', $name);
            }
        } else {
            $name = 'Transformers\\'.$name;
        }

        $class = app()->getNamespace().$name.'Transformer';

        return app($class);
    }

    protected static function setIndexing($transformer) {
        if ($transformer instanceof Transformer) {
            $transformer->isIndexing();
        }

        return $transformer;
    }

    /**
     * @param $item
     *
     * @return Transformer|\Closure
     */
    static public function transformer($item) {
        if ($item instanceof Model) {
            return static::resolveTransformer(get_class($item));
        }

        if ($item instanceof Collection) {
            return static::transformer($item->first());
        }

        if ($item instanceof Paginator) {
            return static::transformer(array_first($item->items()));
        }

        return function ($model) {
            if ($model instanceof Arrayable) {
                return $model->toArray();
            }

            return $model;
        };
    }
}
