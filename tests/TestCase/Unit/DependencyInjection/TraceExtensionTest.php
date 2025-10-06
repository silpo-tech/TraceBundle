<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\DependencyInjection;

use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TraceBundle\DependencyInjection\TraceExtension;

class TraceExtensionTest extends TestCase
{
    private TraceExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new TraceExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadSetsParameters(): void
    {
        $config = [
            [
                'id_header_name' => 'X-Custom-Id',
                'id_log_extra_name' => 'customId',
                'autoconfigure_handlers' => false,
            ],
        ];

        $this->extension->load($config, $this->container);

        $this->assertEquals('X-Custom-Id', $this->container->getParameter('trace.id_header_name'));
        $this->assertEquals('customId', $this->container->getParameter('trace.id_log_extra_name'));
    }

    public function testLoadWithAutoconfigureHandlers(): void
    {
        $config = [
            [
                'id_header_name' => 'X-Some-Id',
                'id_log_extra_name' => 'someId',
                'autoconfigure_handlers' => true,
            ],
        ];

        $this->extension->load($config, $this->container);

        $autoconfiguredClasses = $this->container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey(HandlerStack::class, $autoconfiguredClasses);
        $this->assertTrue($autoconfiguredClasses[HandlerStack::class]->hasTag('trace.traceable_handler'));
    }

    public function testLoadWithoutAutoconfigureHandlers(): void
    {
        $config = [
            [
                'id_header_name' => 'X-Some-Id',
                'id_log_extra_name' => 'someId',
                'autoconfigure_handlers' => false,
            ],
        ];

        $this->extension->load($config, $this->container);

        $autoconfiguredClasses = $this->container->getAutoconfiguredInstanceof();
        $this->assertArrayNotHasKey(HandlerStack::class, $autoconfiguredClasses);
    }
}
