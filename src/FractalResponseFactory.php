<?php


namespace Vice\LaravelFractal;

use ArrayAccess;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;

class FractalResponseFactory
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var FractalService
     */
    private $transformer;

    /**
     * FractalResponseFactory constructor.
     * @param FractalService  $transformer
     * @param ResponseFactory $responseFactory
     */
    public function __construct(FractalService $transformer, ResponseFactory $responseFactory)
    {
        $this->transformer     = $transformer;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param  mixed               $item
     * @param  TransformerAbstract $transformer
     * @param  Closure|null        $metaCallback
     * @return Response
     */
    public function item($item, TransformerAbstract $transformer, Closure $metaCallback = null)
    {
        $scope = $this->transformer->item($item, $transformer, $metaCallback);

        return $this->makeResponse($scope);
    }

    /**
     * @param  array|LengthAwarePaginator|ArrayAccess $items
     * @param  TransformerAbstract                    $transformer
     * @param  Closure|null                           $metaCallback
     * @return Response
     */
    public function collection($items, TransformerAbstract $transformer, Closure $metaCallback = null)
    {
        $scope = $this->transformer->collection($items, $transformer, $metaCallback);

        return $this->makeResponse($scope);
    }

    /**
     * @param array|string $includes
     */
    public function parseIncludes($includes)
    {
        $this->transformer->parseIncludes($includes);
    }

    /**
     * @param  Scope    $scope
     * @return Response
     */
    private function makeResponse(Scope $scope)
    {
        // let the response deal with converting this to JSON for us.
        return $this->responseFactory->make($scope->toArray());
    }
}
