<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use TraceBundle\Generator\TraceIdGeneratorInterface;

trait MockGeneratorTrait
{
    /**
     * @return MockObject | TraceIdGeneratorInterface
     */
    protected function getIdGeneratorMock(string $uuid): MockObject|TraceIdGeneratorInterface
    {
        $generator = $this->createMock(TraceIdGeneratorInterface::class);
        $generator
            ->method('generate')
            ->willReturn($uuid);

        return $generator;
    }
}
