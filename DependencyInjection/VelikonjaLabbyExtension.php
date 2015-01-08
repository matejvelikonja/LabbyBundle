<?php

namespace Velikonja\LabbyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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
        $container->setParameter('velikonja_labby.config.remote', $config['remote']);
        $container->setParameter('velikonja_labby.config.roles', $config['roles']);

        if (! isset($config['password_reset'])) {
            $container->removeDefinition('velikonja_labby.event.listener.password_resetter');
        }

        if ($container->hasDefinition('velikonja_labby.event.listener.password_resetter')) {
            $bundles = $container->getParameter('kernel.bundles');
            if (! isset($bundles['FOSUserBundle'])) {
                throw new InvalidConfigurationException('FOSUserBundle is required for `password_reset` option.');
            }

            $definition = $container->getDefinition('velikonja_labby.event.listener.password_resetter');
            $definition->replaceArgument(1, $config['password_reset']['users']);
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

        if (isset($bundles['DoctrineBundle'])) {
            $doctrineConfig = $container->getExtensionConfig('doctrine');
            $dbalConfig     = $doctrineConfig[0]['dbal'];

            $container->prependExtensionConfig('velikonja_labby', array('db' => $dbalConfig));
        }
    }
}
