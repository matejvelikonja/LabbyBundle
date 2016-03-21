<?php

namespace Velikonja\LabbyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VelikonjaLabbyExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('velikonja_labby.config', $config);
        $container->setParameter('velikonja_labby.config.db', $config['db']);
        $container->setParameter('velikonja_labby.config.fs', $config['fs']);
        $container->setParameter('velikonja_labby.config.event_executors', $config['event_executors']);
        $container->setParameter('velikonja_labby.config.remote', $config['remote']);
        $container->setParameter('velikonja_labby.config.roles', $config['roles']);
        $container->setParameter('velikonja_labby.config.process_timeout', $config['process_timeout']);

        if (! $config['db']['recreate']) {
            $container->removeDefinition('velikonja_labby.event.listener.recreate_database');
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $allowedKeys = array(
            'driver',
            'dbname',
            'host',
            'port',
            'user',
            'password',
            'charset',
        );

        if (isset($bundles['DoctrineBundle'])) {
            $doctrineConfig = $container->getExtensionConfig('doctrine');

            // find first doctrine dbal config and append it to labby config
            $length = count($doctrineConfig);
            for ($i = 0; $i < $length; $i++) {
                if (isset($doctrineConfig[$i]['dbal'])) {
                    $output = array_intersect_key($doctrineConfig[$i]['dbal'], array_flip($allowedKeys));
                    $container->prependExtensionConfig('velikonja_labby', array('db' => $output));
                    break;
                }
            }
        }
    }
}
