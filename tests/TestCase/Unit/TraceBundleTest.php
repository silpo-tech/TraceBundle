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

    public function testConfigureMethodExists(): void
    {
        $bundle = new TraceBundle();
        $reflection = new \ReflectionMethod($bundle, 'configure');
        
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    public function testLoadExtensionMethodExists(): void
    {
        $bundle = new TraceBundle();
        $reflection = new \ReflectionMethod($bundle, 'loadExtension');
        
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(3, $reflection->getNumberOfParameters());
    }
}
