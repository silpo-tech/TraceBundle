<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TraceBundle\TraceBundle;

class TraceBundleTest extends TestCase
{
    public function testBuild(): void
    {
        $bundle = new TraceBundle();
        $container = new ContainerBuilder();

        $initialPassCount = count($container->getCompilerPassConfig()->getPasses());
        $bundle->build($container);
        $finalPassCount = count($container->getCompilerPassConfig()->getPasses());

        $this->assertEquals(2, $finalPassCount - $initialPassCount);
    }
}
