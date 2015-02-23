<?php

namespace Velikonja\LabbyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExecutorCompilerPass implements CompilerPassInterface{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('velikonja_labby.executor.executor_runner')) {
            return;
        }

        $definition = $container->getDefinition(
            'velikonja_labby.executor.executor_runner'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'velikonja_labby.executor'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addExecutor',
                array(
                    new Reference($id)
                )
            );
        }
    }
}
