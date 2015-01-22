<?php

namespace Velikonja\LabbyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use SyncFS\Configuration\Configuration as SyncFSConfiguration;

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
                ->arrayNode('password_reset')
                    ->children()
                        ->arrayNode('users')
                            ->defaultValue(array(array('username' => 'admin', 'password' => 'admin')))
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('username')->isRequired()->defaultValue('admin')->end()
                                    ->scalarNode('password')->isRequired()->defaultValue('admin')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('roles')
                    ->treatNullLike(array())
                    ->prototype('scalar')->end()
                    ->defaultValue(array('remote'))
                ->end()
                ->arrayNode('remote')
                    ->children()
                        ->scalarNode('hostname')->end()
                        ->scalarNode('path')->end()
                        ->scalarNode('env')->defaultValue('prod')->end()
                    ->end()
                ->end()
                ->append(
                    $this->addSyncFsNode()
                )
                ->arrayNode('db')
                    // copy from doctrine dbal config
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
