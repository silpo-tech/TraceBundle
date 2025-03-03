<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Integration\DependencyInjection;

use Enqueue\Bundle\EnqueueBundle;
use MessageBusBundle\DependencyInjection\MessageBusExtension;
use MessageBusBundle\MessageBusBundle;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TraceBundle\DependencyInjection\MessageBusCompilerPass;
use TraceBundle\Tests\TestKernel;
use TraceBundle\TraceBundle;

class ConfigurationTest extends KernelTestCase
{
    #[DataProvider('messageBusCompilerPassDataProvider')]
    public function testMessageBusCompilerPass(array $configs, array $bundles, bool $hasDefinition = true): void
    {
        $kernel = new TestKernel('test', true, $bundles, $configs);
        $kernel->boot();
        $builder = $kernel->getContainerBuilder();
        $this->assertFalse($builder->hasDefinition('trace.message_bus.event_subscriber'));
        if ($hasDefinition) {
            $builder->registerExtension(new MessageBusExtension());
        }
        $cp = new MessageBusCompilerPass();
        $cp->process($builder);

        $this->assertEquals($hasDefinition, $builder->hasDefinition('trace.message_bus.event_subscriber'));
    }

    public static function messageBusCompilerPassDataProvider(): iterable
    {
        yield 'test with MessageBusBundle and EnqueueBundle' => [
            'configs' => TestKernel::MESSAGE_BUS_CONFIGS,
            'bundles' => [TraceBundle::class, FrameworkBundle::class, MessageBusBundle::class, EnqueueBundle::class],
            'hasDefinition' => true,
        ];

        yield 'test without MessageBusBundle and EnqueueBundle' => [
            'configs' => TestKernel::DEFAULT_CONFIGS,
            'bundles' => [TraceBundle::class, FrameworkBundle::class],
            'hasDefinition' => false,
        ];
    }
}
