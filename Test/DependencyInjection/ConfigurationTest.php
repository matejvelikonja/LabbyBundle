<?php

namespace Velikonja\LabbyBundle\Test\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;
use Velikonja\LabbyBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests example configuration. Will throw exception if does not validate.
     *
     * @param string $path
     *
     * @dataProvider getValidConfigurationsPaths
     */
    public function testValidConfigurations($path)
    {
        $config        = Yaml::parse($path);
        $processor     = new Processor();
        $configuration = new Configuration();

        $processor->processConfiguration(
            $configuration,
            $config
        );

        $this->assertTrue(true);
    }

    /**
     * @param string $path
     *
     * @dataProvider getInvalidConfigurationsPaths
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidConfigurations($path)
    {
        $config        = Yaml::parse($path);
        $processor     = new Processor();
        $configuration = new Configuration();

        $processor->processConfiguration(
            $configuration,
            $config
        );

        $this->assertTrue(true);
    }

    /**
     * @return array
     */
    public function getValidConfigurationsPaths()
    {
        return $this->getConfigurationsPaths('valid');
    }

    /**
     * @return array
     */
    public function getInvalidConfigurationsPaths()
    {
        return $this->getConfigurationsPaths('invalid');
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getConfigurationsPaths($type)
    {
        $files = glob(__DIR__ . sprintf('/../fixtures/configs/%s/*.yml', $type));
        $args  = array();

        foreach ($files as $file) {
            $args[] = array($file);
        }

        return $args;
    }
}
