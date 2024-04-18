<?php

declare(strict_types=1);

namespace TraceBundle;

use GuzzleHttp\HandlerStack;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use TraceBundle\DependencyInjection\HttpClientMiddlewareCompilerPass;

class TraceBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        // @phpstan-ignore-next-line
        $definition
            ->rootNode()
            ->children()
            ->scalarNode('id_header_name')->defaultValue('X-Request-Id')->end()
            ->scalarNode('id_log_extra_name')->defaultValue('requestId')->end()
            ->booleanNode('autoconfigure_handler_stacks')->defaultTrue()->end()
            ->end()
        ;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new HttpClientMiddlewareCompilerPass());
    }

    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.yaml');

        if ($config['autoconfigure_handler_stacks']) {
            $containerBuilder->registerForAutoconfiguration(HandlerStack::class)
                ->addTag('trace.handler_stack')
            ;
        }

        $containerConfigurator->parameters()
            ->set('trace.id_header_name', $config['id_header_name'])
            ->set('trace.id_log_extra_name', $config['id_log_extra_name'])
        ;
    }
}
