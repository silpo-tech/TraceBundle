<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use TraceBundle\DependencyInjection\HttpClientMiddlewareCompilerPass;
use TraceBundle\DependencyInjection\MessageBusCompilerPass;

class DependencyInjectionTest extends TestCase
{
    public function testHttpClientMiddlewareCompilerPassProcess(): void
    {
        $container = new ContainerBuilder();

        $middlewareDefinition = new Definition();
        $container->setDefinition('trace.client.middleware', $middlewareDefinition);

        $sentryMiddlewareDefinition = new Definition();
        $container->setDefinition('trace.client.middleware_sentry', $sentryMiddlewareDefinition);

        $handlerDefinition = new Definition();
        $handlerDefinition->addTag('trace.traceable_handler');
        $container->setDefinition('test.handler', $handlerDefinition);

        $compilerPass = new HttpClientMiddlewareCompilerPass();
        $compilerPass->process($container);

        $handler = $container->getDefinition('test.handler');
        $methodCalls = $handler->getMethodCalls();

        $this->assertGreaterThanOrEqual(1, count($methodCalls));
        $this->assertEquals('push', $methodCalls[0][0]);
        $this->assertEquals('trace', $methodCalls[0][1][1]);
    }

    public function testMessageBusCompilerPassWithoutMessageBusBundle(): void
    {
        $container = new ContainerBuilder();

        $compilerPass = new MessageBusCompilerPass();
        $compilerPass->process($container);

        $this->assertFalse($container->hasDefinition('trace.message_bus.event_subscriber'));
    }

    public function testMessageBusCompilerPassWithMessageBusBundle(): void
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new class extends \Symfony\Component\DependencyInjection\Extension\Extension {
            public function getAlias(): string
            {
                return 'message_bus';
            }

            public function load(array $configs, ContainerBuilder $container): void
            {
            }
        });

        $storageDefinition = new Definition();
        $container->setDefinition('trace.storage', $storageDefinition);

        $generatorDefinition = new Definition();
        $container->setDefinition('trace.generator', $generatorDefinition);

        $container->setParameter('trace.id_header_name', 'X-Request-Id');

        $compilerPass = new MessageBusCompilerPass();
        $compilerPass->process($container);

        $this->assertTrue($container->hasDefinition('trace.message_bus.event_subscriber'));
        $definition = $container->getDefinition('trace.message_bus.event_subscriber');
        $this->assertTrue($definition->hasTag('kernel.event_subscriber'));
    }
}
