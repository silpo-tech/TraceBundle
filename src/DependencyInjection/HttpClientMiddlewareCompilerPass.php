<?php

declare(strict_types=1);

namespace TraceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class HttpClientMiddlewareCompilerPass implements CompilerPassInterface
{
    private const TRACE_MIDDLEWARE_NAME = 'trace';

    public function process(ContainerBuilder $container): void
    {
        $middleWare = $container->getDefinition('trace.client.middleware');

        $handlerStacks = $container->findTaggedServiceIds('trace.handler_stack');

        foreach ($handlerStacks as $id => $tags) {
            $handlerStack = $container->getDefinition($id);

            $handlerStack->addMethodCall('push', [$middleWare, self::TRACE_MIDDLEWARE_NAME]);
        }
    }
}
