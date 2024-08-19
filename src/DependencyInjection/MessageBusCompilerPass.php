<?php

declare(strict_types=1);

namespace TraceBundle\DependencyInjection;

use MessageBusBundle\MessageBusBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use TraceBundle\EventSubscriber\MessageBusSubscriber;

final class MessageBusCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!class_exists(MessageBusBundle::class) || !$container->hasExtension('message_bus')) {
            return;
        }

        $def = new Definition(MessageBusSubscriber::class);
        $def->addTag('kernel.event_subscriber');
        $def->setArguments([
            new Reference('trace.storage'),
            new Reference('trace.generator'),
            new Parameter('trace.id_header_name'),
        ]);
        $container->setDefinition('trace.message_bus.event_subscriber', $def);
    }
}
