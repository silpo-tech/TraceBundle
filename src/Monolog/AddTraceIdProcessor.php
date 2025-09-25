<?php

declare(strict_types=1);

namespace TraceBundle\Monolog;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use TraceBundle\Storage\TraceIdStorageInterface;

final class AddTraceIdProcessor implements ProcessorInterface
{
    public function __construct(private readonly TraceIdStorageInterface $traceIdStorage, private readonly string $extraFieldName)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $traceId = $this->traceIdStorage->get();

        if (null !== $traceId) {
            $record['extra'][$this->extraFieldName] = $this->traceIdStorage->get();
        }

        return $record;
    }
}
