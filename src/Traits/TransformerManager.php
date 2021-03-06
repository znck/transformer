<?php namespace Znck\Transformers\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorWrapper;
use Illuminate\Pagination\Paginator as LaravelPaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\ArraySerializer;
use Znck\Transformers\ArrayableTransformer;
use Znck\Transformers\Transformer;


trait TransformerManager
{
    protected static $manager;

    public static function response($data, $includes = null, $excludes = null, $guess = false) {
        $manager = self::getManager();

        if ($data instanceof Collection) {
            $resource = self::transformer($data)->collection($data, null, $guess ? self::guessResourceKey($data) : null);
        } elseif ($data instanceof Paginator) {
            $resource = self::paginatedResponse($data, $guess ? self::guessResourceKey($data) : null);
        } elseif ($data instanceof Model) {
            $resource = self::transformer($data)->item($data, null, $guess ? self::guessResourceKey($data) : null);
        }

        if (! isset($resource)) {
            return $data;
        }

        return $manager
            ->parseIncludes($includes ?? app(Request::class)->query('with', ''))
            ->parseExcludes($excludes ?? app(Request::class)->query('without', ''))
            ->createData($resource)->toArray();
    }

    /**
     * @return Manager
     */
    public static function getManager() {
        if (is_null(self::$manager)) {
            self::$manager = new Manager();
            self::$manager->setSerializer(new ArraySerializer());
        }

        return self::$manager;
    }

    /**
     * @param $item
     *
     * @return Transformer
     */
    public static function transformer($item) {
        if ($item instanceof Model) {
            return self::resolveTransformer(get_class($item));
        }

        if ($item instanceof Collection) {
            return self::transformer($item->first());
        }

        if ($item instanceof Paginator) {
            return self::transformer(array_first($item->items()));
        }

        return app(ArrayableTransformer::class);
    }

    /**
     * Handle paginated response.
     *
     * @param Paginator $data
     *
     * @param string|null $resourceKey
     *
     * @return mixed
     */
    protected static function paginatedResponse(Paginator $data, $resourceKey) {
        $resource = self::transformer($data)->collection($data->items(), null, $resourceKey);

        $resource->setPaginator(
            $data instanceof LengthAwarePaginator
                ? new IlluminatePaginatorAdapter($data)
                : new IlluminatePaginatorAdapter(self::wrapPaginator($data))
        );

        return $resource;
    }

    /**
     * @param \Illuminate\Contracts\Pagination\Paginator $data
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected static function wrapPaginator(Paginator $data):\Illuminate\Pagination\LengthAwarePaginator {
        $before = $data->perPage() * ($data->currentPage() - 1);
        $after = $data->perPage() === count($data->items()) ? 1 : 0;

        return new PaginatorWrapper(
            $data->items(),
            $before + count($data->items()) + $after,
            $data->perPage(),
            $data->currentPage(),
            [
                'path' => LaravelPaginator::resolveCurrentPath(),
                'pageName' => $data->pageName ?? 'page',
            ]
        );
    }

    /**
     * @param $data
     *
     * @return string
     */
    protected static function guessResourceKey($data): string {
        if ($data instanceof Model) {
            return strtolower(snake_case(class_basename($data)));
        }

        if ($data instanceof Collection) {
            return str_plural(self::guessResourceKey($data->first()));
        }

        if ($data instanceof Paginator) {
            return str_plural(self::guessResourceKey(array_first($data->items())));
        }

        return 'item';
    }
}
