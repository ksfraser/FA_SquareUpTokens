<?php

namespace FA_SquareUpTokens\Tests;

use PHPUnit\Framework\TestCase;
use FA_SquareUpTokens\Interfaces\ContainerInterface;
use FA_SquareUpTokens\Container;

class ContainerTest extends TestCase
{
    public function testSetAndGetService()
    {
        $container = new Container();
        $container->set('test', function() {
            return 'value';
        });

        $result = $container->get('test');

        $this->assertEquals('value', $result);
    }

    public function testGetServiceWithDependencies()
    {
        $container = new Container();
        $container->set('dep', function() {
            return 'dependency';
        });
        $container->set('service', function($c) {
            return 'service with ' . $c->get('dep');
        });

        $result = $container->get('service');

        $this->assertEquals('service with dependency', $result);
    }

    public function testGetThrowsExceptionForUnknownService()
    {
        $container = new Container();

        $this->expectException(\Exception::class);
        $container->get('unknown');
    }
}