<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\Generator;

use PHPUnit\Framework\TestCase;
use TraceBundle\Generator\UuidGenerator;

class UuidGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $generator = new UuidGenerator();
        $uuid = $generator->generate();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid);
    }

    public function testGenerateUnique(): void
    {
        $generator = new UuidGenerator();
        $uuid1 = $generator->generate();
        $uuid2 = $generator->generate();

        $this->assertNotEquals($uuid1, $uuid2);
    }
}
