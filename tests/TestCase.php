<?php


namespace tests\Vice\LaravelFractal;

use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        m::close();
    }
}
