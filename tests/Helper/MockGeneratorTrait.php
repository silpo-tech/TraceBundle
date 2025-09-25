<?php

declare(strict_types=1);

namespace TraceBundle\Tests\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use TraceBundle\Generator\TraceIdGeneratorInterface;

trait MockGeneratorTrait
{
    protected function getIdGeneratorMock(string $uuid): MockObject|TraceIdGeneratorInterface
    {
        $generator = $this->createMock(TraceIdGeneratorInterface::class);
        $generator
            ->method('generate')
            ->willReturn($uuid);

        return $generator;
    }
}
