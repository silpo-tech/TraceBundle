<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit;

use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use TraceBundle\Monolog\AddTraceIdProcessor;
use TraceBundle\Storage\TraceIdStorage;

class AddTraceIdProcessorTest extends TestCase
{
    public function testInvokeWithTraceId(): void
    {
        $traceId = 'test-trace-id';
        $fieldName = 'requestId';
        $storage = new TraceIdStorage();
        $storage->set($traceId);

        $processor = new AddTraceIdProcessor($storage, $fieldName);
        $record = new LogRecord(
            new \DateTimeImmutable(),
            'test',
            \Monolog\Level::Info,
            'Test message',
            []
        );

        $result = $processor($record);

        $this->assertEquals($traceId, $result['extra'][$fieldName]);
    }

    public function testInvokeWithoutTraceId(): void
    {
        $fieldName = 'requestId';
        $storage = new TraceIdStorage();

        $processor = new AddTraceIdProcessor($storage, $fieldName);
        $record = new LogRecord(
            new \DateTimeImmutable(),
            'test',
            \Monolog\Level::Info,
            'Test message',
            []
        );

        $result = $processor($record);

        $this->assertArrayNotHasKey($fieldName, $result['extra']);
    }
}
