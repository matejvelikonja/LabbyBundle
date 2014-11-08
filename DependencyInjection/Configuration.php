<?php

namespace Velikonja\LabbyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use SyncFS\Configuration\Configuration as SyncFSConfiguration;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('velikonja_labby');

        $rootNode
            ->children()
                ->arrayNode('roles')
                    ->treatNullLike(array())
                    ->prototype('scalar')->end()
                    ->defaultValue(array('remote'))
                ->end()
                ->arrayNode('remote')
                    ->children()
                        ->scalarNode('hostname')->end()
                        ->scalarNode('path')->end()
                    ->end()
                ->end()
                ->append(
                    $this->addSyncFsNode()
                )
                ->arrayNode('db')
                    ->children()
                        ->scalarNode('driver')->end()
                        ->scalarNode('dbname')->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->scalarNode('port')->defaultNull()->end()
                        ->scalarNode('user')->defaultValue('root')->end()
                        ->scalarNode('password')->defaultNull()->end()
                        ->scalarNode('charset')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * Returns configuration node of Velikonja/SyncFS library.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    private function addSyncFsNode()
    {
        $config  = new SyncFSConfiguration();
        $builder = new TreeBuilder();
        $node    = $builder->root('fs');

        return $config->getConfigNode($node);
    }
}
