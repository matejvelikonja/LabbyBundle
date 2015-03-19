<?php

namespace Velikonja\LabbyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use SyncFS\Configuration\Configuration as SyncFSConfiguration;
use Velikonja\LabbyBundle\Events;

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
                ->scalarNode('process_timeout')->defaultValue(5 * 60)->end()
                ->arrayNode('roles')
                    ->treatNullLike(array())
                    ->prototype('scalar')->end()
                    ->defaultValue(array('remote'))
                ->end()
                ->arrayNode('remote')
                    ->isRequired()
                    ->children()
                        ->scalarNode('hostname')->isRequired()->end()
                        ->scalarNode('path')->isRequired()->end()
                        ->scalarNode('env')->defaultValue('prod')->end()
                    ->end()
                ->end()
                ->append(
                    $this->addSyncFsNode()
                )
                ->arrayNode('db')
                    ->children()
                        ->booleanNode('recreate')->defaultValue(true)->end()
                        // copy from doctrine dbal config
                        ->scalarNode('driver')->end()
                        ->scalarNode('dbname')->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->scalarNode('port')->defaultNull()->end()
                        ->scalarNode('user')->defaultValue('root')->end()
                        ->scalarNode('password')->defaultNull()->end()
                        ->scalarNode('charset')->end()
                    ->end()
                ->end()
                ->arrayNode('event_executors')
                    ->useAttributeAsKey('event_name')
                    ->beforeNormalization()
                        ->always()
                        // prefix all events with bundle name
                        ->then(function ($events) {
                            $normalized = array();
                            foreach ($events as $name => $value) {
                                $normalized['velikonja_labby.' . $name] = $value;
                            }
                            return $normalized;
                        })
                    ->end()
                    ->validate()
                        ->ifTrue(function ($events) {
                            $allEvents = Events::all();
                            foreach ($events as $name => $value) {
                                if (! in_array($name, $allEvents)) {
                                    return true;
                                }
                            }

                            return false;
                        })
                        ->thenInvalid("Event does not exists. \n%s")
                    ->end()
                    ->prototype('array')
                        ->prototype('array')
                            ->useAttributeAsKey('type')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
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
