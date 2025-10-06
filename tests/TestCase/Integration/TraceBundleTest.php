<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Integration;

use PHPUnit\Framework\TestCase;
use TraceBundle\Storage\TraceIdStorage;
use TraceBundle\Tests\TestKernel;

class TraceBundleTest extends TestCase
{
    public function testAutowired()
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->assertInstanceOf(TraceIdStorage::class, $container->get('trace.storage'));
        $this->assertEquals('X-Some-Id', $container->getParameter('trace.id_header_name'));
        $this->assertEquals('someId', $container->getParameter('trace.id_log_extra_name'));
        $this->assertTrue($container->has('trace.client.middleware_sentry'));
        $this->assertTrue($container->has('trace.client.middleware'));
    }
}
