<?php

declare(strict_types=1);

namespace TraceBundle\DependencyInjection;

use Sentry\Tracing\GuzzleTracingMiddleware;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class HttpClientMiddlewareCompilerPass implements CompilerPassInterface
{
    private const TRACE_MIDDLEWARE_NAME = 'trace';
    private const SENTRY_TRACE_MIDDLEWARE_NAME = 'sentry-trace';

    public function process(ContainerBuilder $container): void
    {
        $middleWare = $container->getDefinition('trace.client.middleware');

        $handlers = $container->findTaggedServiceIds('trace.traceable_handler');

        $middlewares = [
            self::TRACE_MIDDLEWARE_NAME => $middleWare,
        ];

        if (class_exists(GuzzleTracingMiddleware::class)) {
            $middlewares[self::SENTRY_TRACE_MIDDLEWARE_NAME] = $container->getDefinition('trace.client.middleware_sentry');
        }

        foreach ($handlers as $id => $tags) {
            $handler = $container->getDefinition($id);

            foreach ($middlewares as $name => $mw) {
                $handler->addMethodCall('push', [$mw, $name]);
            }
        }
    }
}
