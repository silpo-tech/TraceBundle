<?php

declare(strict_types=1);

namespace TraceBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TraceBundle\DependencyInjection\HttpClientMiddlewareCompilerPass;
use TraceBundle\DependencyInjection\MessageBusCompilerPass;

class TraceBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new HttpClientMiddlewareCompilerPass());
        $container->addCompilerPass(new MessageBusCompilerPass());
    }
}
