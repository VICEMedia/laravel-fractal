<?php


namespace Vice\LaravelFractal;

use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;

class FractalService
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * FractalService constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param  mixed               $item
     * @param  TransformerAbstract $transformer
     * @param  Closure|null        $metaCallback
     * @return Scope
     */
    public function item($item, TransformerAbstract $transformer, Closure $metaCallback = null)
    {
        $resource = new Item($item, $transformer);

        if (!is_null($metaCallback)) {
            call_user_func($metaCallback, $resource);
        }

        return $this->manager->createData($resource);
    }

    /**
     * @param $items
     * @param  TransformerAbstract $transformer
     * @param  Closure|null        $metaCallback
     * @return Scope
     */
    public function collection($items, TransformerAbstract $transformer, Closure $metaCallback = null)
    {
        $resource = $this->getCollectionResource($items, $transformer);

        if (!is_null($metaCallback)) {
            call_user_func($metaCallback, $resource);
        }

        return $this->manager->createData($resource);
    }

    /**
     * @param array|string $includes
     */
    public function parseIncludes($includes)
    {
        $this->manager->parseIncludes($includes);
    }

    /**
     * @param $items
     * @param  TransformerAbstract $transformer
     * @return Collection
     */
    private function getCollectionResource($items, TransformerAbstract $transformer)
    {
        if ($items instanceof LengthAwarePaginator) {
            $resource = new Collection($items->getCollection(), $transformer);
            $resource->setPaginator(new IlluminatePaginatorAdapter($items));

            return $resource;
        }

        return new Collection($items, $transformer);
    }
}
