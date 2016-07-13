<?php


namespace tests\Vice\LaravelFractal;


use Illuminate\Http\Response;
use Vice\LaravelFractal\ResponseFactory;

class ResponseFactoryTest extends TestCase
{
    public function testProducesAResponse()
    {
        $factory = new ResponseFactory();
        $response = $factory->make(['foo' => 'bar'], 200, ['header' => 'content']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(['foo' => 'bar'], $response->getOriginalContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('header', $response->headers->all());
        $this->assertEquals('content', $response->headers->get('header'));
    }
}
