<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\Storage;

use PHPUnit\Framework\TestCase;
use TraceBundle\Storage\TraceIdStorage;

class TraceIdStorageTest extends TestCase
{
    public function testGetSetTraceId(): void
    {
        $storage = new TraceIdStorage();
        $traceId = 'test-trace-id';

        $this->assertNull($storage->get());

        $result = $storage->set($traceId);
        $this->assertSame($storage, $result);
        $this->assertEquals($traceId, $storage->get());
    }

    public function testReset(): void
    {
        $storage = new TraceIdStorage();
        $storage->set('test-trace-id');

        $this->assertNotNull($storage->get());

        $storage->reset();
        $this->assertNull($storage->get());
    }
}
