<?php

declare(strict_types=1);

namespace TraceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('trace');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('id_header_name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('id_log_extra_name')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('autoconfigure_handlers')->defaultTrue()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
