<?php


namespace tests\Vice\LaravelFractal;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use League\Fractal\Manager;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\TransformerAbstract;
use Mockery as m;
use Vice\LaravelFractal\FractalResponseFactory;
use Vice\LaravelFractal\FractalService;
use Vice\LaravelFractal\ResponseFactory;

class FractalResponseFactoryTest extends TestCase
{
    /**
     * @var FractalResponseFactory
     */
    private $service;
    /**
     * @var FractalService
     */
    private $transformer;
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function setUp()
    {
        parent::setUp();

        $this->initTransformer();
        $this->initResponseFactory();
        $this->service = new FractalResponseFactory($this->transformer, $this->responseFactory);
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    public function testItem()
    {
        /** @var array $transformedData */
        $transformedData = $this->service->item(new ExampleItem(), new ExampleItemTransformer());

        $this->assertArrayHasKey('data', $transformedData);
        $dataScope = $transformedData['data'];

        $this->assertArrayHasKey('name', $dataScope);
        $this->assertEquals($dataScope['name'], 'name');
    }

    public function testItemWithClosure()
    {
        /** @var array $transformedData */
        $transformedData = $this->service->item(new ExampleItem(), new ExampleItemTransformer(),
            function (ResourceAbstract $resourceInterface) {
                $resourceInterface->setMetaValue('foo', 'bar');
            });

        $this->assertArrayHasKey('meta', $transformedData);
        $metaScope = $transformedData['meta'];

        $this->assertArrayHasKey('foo', $metaScope);
        $this->assertEquals($metaScope['foo'], 'bar');
    }

    public function testCollectionFromArray()
    {
        /** @var array $transformedData */
        $transformedData = $this->service->collection([new ExampleItem(), new ExampleItem()],
            new ExampleItemTransformer());

        $this->assertArrayHasKey('data', $transformedData);
        $dataScope = $transformedData['data'];

        $this->assertCount(2, $dataScope);
        $this->assertSame(array_first($dataScope, function () {
            return true;
        })['name'], 'name');
    }

    public function testCollectionFromCollection()
    {
        $collection = new Collection([new ExampleItem(), new ExampleItem()]);

        /** @var array $transformedData */
        $transformedData = $this->service->collection($collection,
            new ExampleItemTransformer());

        $this->assertArrayHasKey('data', $transformedData);
        $dataScope = $transformedData['data'];

        $this->assertCount(2, $dataScope);
        $this->assertSame(array_first($dataScope, function () {
            return true;
        })['name'], 'name');
    }

    public function testCollectionWithClosure()
    {
        /** @var array $transformedData */
        $transformedData = $this->service->collection([new ExampleItem(), new ExampleItem()],
            new ExampleItemTransformer(),
            function (ResourceAbstract $resourceInterface) {
                $resourceInterface->setMetaValue('foo', 'bar');
            });

        $this->assertArrayHasKey('meta', $transformedData);
        $metaScope = $transformedData['meta'];

        $this->assertArrayHasKey('foo', $metaScope);
        $this->assertEquals($metaScope['foo'], 'bar');
    }

    public function testCollectionFromPaginator()
    {
        $collection = new LengthAwarePaginator([new ExampleItem(), new ExampleItem()], 4, 2, 1);

        /** @var array $transformedData */
        $transformedData = $this->service->collection($collection, new ExampleItemTransformer());

        $this->assertArrayHasKey('data', $transformedData);
        $dataScope = $transformedData['data'];

        $this->assertCount(2, $dataScope);
        $this->assertSame(array_first($dataScope, function () {
            return true;
        })['name'], 'name');

        $this->assertArrayHasKey('meta', $transformedData);
        $metaScope = $transformedData['meta'];

        $this->assertArrayHasKey('pagination', $metaScope);
        $paginationScope = $metaScope['pagination'];

        $this->assertEquals($paginationScope['total'], 4);
        $this->assertEquals($paginationScope['count'], 2);
        $this->assertEquals($paginationScope['per_page'], 2);
    }

    public function testParseIncludes()
    {
        $transformer = m::mock(FractalService::class);
        $transformer->shouldReceive('parseIncludes')->with('foo');

        $service = new FractalResponseFactory($transformer, m::mock(ResponseFactory::class));
        $service->parseIncludes('foo');
    }

    private function initTransformer()
    {
        $this->transformer = new FractalService(new Manager());
    }

    private function initResponseFactory()
    {
        $this->responseFactory = m::mock(ResponseFactory::class)
            ->shouldReceive('make')
            ->with(\Mockery::type('array'))
            ->andReturnUsing(function ($array) {
                return $array;
            })
            ->getMock();
    }
}

class ExampleItem
{
    public function getName()
    {
        return 'name';
    }
}

class ExampleItemTransformer extends TransformerAbstract
{
    public function transform(ExampleItem $exampleItem)
    {
        return [
            'name' => $exampleItem->getName()
        ];
    }
}
