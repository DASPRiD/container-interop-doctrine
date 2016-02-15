<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Doctrine;

use Interop\Container\ContainerInterface;

abstract class AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function __invoke(ContainerInterface $container)
    {
        return $this->createWithConfig($container, 'orm_default');
    }

    /**
     * Creates a new instance from a specified config, specifically meant to be used as static factory.
     *
     * In case you want to use another config key than "orm_default", you can add the following factory to your config:
     *
     * <code>
     * <?php
     * return [
     *     'doctrine.SECTION.orm_other' => [SpecificFactory::class, 'orm_other'],
     * ];
     * </code>
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception\DomainException
     */
    public static function __callStatic($name, array $arguments)
    {
        if (!array_key_exists(0, $arguments) || !$arguments[0] instanceof ContainerInterface) {
            throw new Exception\DomainException(sprintf(
                'The first argument must be of type %s',
                ContainerInterface::class
            ));
        }

        return (new static())->createWithConfig($arguments[0], $name);
    }

    /**
     * Creates a new instance from a specified config.
     *
     * @param ContainerInterface $container
     * @param string $configKey
     * @return mixed
     */
    abstract public function createWithConfig(ContainerInterface $container, $configKey);

    /**
     * Retrieves the config for a specific section.
     *
     * @param ContainerInterface $container
     * @param string $configKey
     * @param string $section
     * @return array
     */
    protected function retrieveConfig(ContainerInterface $container, $configKey, $section)
    {
        $applicationConfig = $container->has('config') ? $container->get('config') : [];
        $doctrineConfig = array_key_exists('doctrine', $applicationConfig) ? $applicationConfig['doctrine'] : [];
        $sectionConfig = array_key_exists($section, $doctrineConfig) ? $doctrineConfig[$section] : [];

        if (array_key_exists($configKey, $sectionConfig)) {
            return $sectionConfig[$configKey];
        }

        return [];
    }
}
