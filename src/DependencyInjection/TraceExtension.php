<?php

declare(strict_types=1);

namespace TraceBundle\DependencyInjection;

use GuzzleHttp\HandlerStack;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class TraceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        if ($config['autoconfigure_handlers']) {
            $container->registerForAutoconfiguration(HandlerStack::class)
                ->addTag('trace.traceable_handler')
            ;
        }

        $container->setParameter('trace.id_header_name', $config['id_header_name']);
        $container->setParameter('trace.id_log_extra_name', $config['id_log_extra_name']);
    }
}
