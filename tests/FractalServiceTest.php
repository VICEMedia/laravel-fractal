<?php


namespace tests\Vice\LaravelFractal;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use League\Fractal\Manager;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;
use Mockery as m;
use Vice\LaravelFractal\FractalService;

class FractalServiceTest extends TestCase
{
    /**
     * @var FractalService
     */
    private $service;
    /**
     * @var Manager
     */
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $this->initManager();
        $this->service = new FractalService($this->manager);
    }

    public function testItem()
    {
        $scope = $this->service->item(new TransformationExampleItem(), new TransformationExampleItemTransformer());

        $this->assertInstanceOf(Scope::class, $scope);

        $transformedData = $scope->toArray();

        $this->assertArrayHasKey('data', $transformedData);
        $dataScope = $transformedData['data'];

        $this->assertArrayHasKey('name', $dataScope);
        $this->assertEquals($dataScope['name'], 'name');
    }

    public function testItemWithClosure()
    {
        $scope = $this->service->item(new TransformationExampleItem(), new TransformationExampleItemTransformer(),
            function (ResourceAbstract $resourceInterface) {
                $resourceInterface->setMetaValue('foo', 'bar');
            });

        $this->assertInstanceOf(Scope::class, $scope);

        $transformedData = $scope->toArray();

        $this->assertArrayHasKey('meta', $transformedData);
        $metaScope = $transformedData['meta'];

        $this->assertArrayHasKey('foo', $metaScope);
        $this->assertEquals($metaScope['foo'], 'bar');
    }

    public function testCollectionFromArray()
    {
        $scope = $this->service->collection([new TransformationExampleItem(), new TransformationExampleItem()],
            new TransformationExampleItemTransformer());

        $this->assertInstanceOf(Scope::class, $scope);

        $transformedData = $scope->toArray();

        $this->assertArrayHasKey('data', $transformedData);
        $dataScope = $transformedData['data'];

        $this->assertCount(2, $dataScope);
        $this->assertSame(array_first($dataScope, function () {
            return true;
        })['name'], 'name');
    }

    public function testCollectionFromCollection()
    {
        $collection = new Collection([new TransformationExampleItem(), new TransformationExampleItem()]);

        $scope = $this->service->collection($collection,
            new TransformationExampleItemTransformer());

        $this->assertInstanceOf(Scope::class, $scope);

        $transformedData = $scope->toArray();

        $this->assertArrayHasKey('data', $transformedData);
        $dataScope = $transformedData['data'];

        $this->assertCount(2, $dataScope);
        $this->assertSame(array_first($dataScope, function () {
            return true;
        })['name'], 'name');
    }

    public function testCollectionWithClosure()
    {
        $scope = $this->service->collection([new TransformationExampleItem(), new TransformationExampleItem()],
            new TransformationExampleItemTransformer(),
            function (ResourceAbstract $resourceInterface) {
                $resourceInterface->setMetaValue('foo', 'bar');
            });

        $this->assertInstanceOf(Scope::class, $scope);

        $transformedData = $scope->toArray();

        $this->assertArrayHasKey('meta', $transformedData);
        $metaScope = $transformedData['meta'];

        $this->assertArrayHasKey('foo', $metaScope);
        $this->assertEquals($metaScope['foo'], 'bar');
    }

    public function testCollectionFromPaginator()
    {
        $collection = new LengthAwarePaginator([new TransformationExampleItem(), new TransformationExampleItem()], 4, 2, 1);

        $scope = $this->service->collection($collection, new TransformationExampleItemTransformer());

        $this->assertInstanceOf(Scope::class, $scope);

        $transformedData = $scope->toArray();

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
        /** @var Manager|m\MockInterface $manager */
        $manager = m::mock(Manager::class);
        $manager->shouldReceive('parseIncludes')->with(['foo'])->once();

        $service = new FractalService($manager);

        $service->parseIncludes(['foo']);
    }

    private function initManager()
    {
        $this->manager = new Manager();
    }
}

class TransformationExampleItem
{
    public function getName()
    {
        return 'name';
    }
}

class TransformationExampleItemTransformer extends TransformerAbstract
{
    public function transform(TransformationExampleItem $exampleItem)
    {
        return [
            'name' => $exampleItem->getName()
        ];
    }
}
