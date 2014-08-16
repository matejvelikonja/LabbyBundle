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
        $rootNode = $treeBuilder->root('velikonja_labby');

        $rootNode
            ->children()
                ->append($this->addSyncFsNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * Returns configuration node of Velikona/SyncFS library.
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
