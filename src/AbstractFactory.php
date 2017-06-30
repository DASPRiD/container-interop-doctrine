<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrine;

use Psr\Container\ContainerInterface;

abstract class AbstractFactory
{
    /**
     * @var string
     */
    private $configKey;

    /**
     * @param string $configKey
     */
    public function __construct($configKey = 'orm_default')
    {
        $this->configKey = $configKey;
    }

    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function __invoke(ContainerInterface $container)
    {
        return $this->createWithConfig($container, $this->configKey);
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

        return (new static($name))->__invoke($arguments[0]);
    }

    /**
     * Creates a new instance from a specified config.
     *
     * @param ContainerInterface $container
     * @param string $configKey
     * @return mixed
     */
    abstract protected function createWithConfig(ContainerInterface $container, $configKey);

    /**
     * Returns the default config.
     *
     * @param string $configKey
     * @return array
     */
    abstract protected function getDefaultConfig($configKey);

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
            return $sectionConfig[$configKey] + $this->getDefaultConfig($configKey);
        }

        return $this->getDefaultConfig($configKey);
    }

    /**
     * Retrieves a dependency through the container.
     *
     * If the container does not know about the dependency, it is pulled from a fresh factory. This saves the user from
     * registering factories which they are not gonna access themself at all, and thus minimized configuration.
     *
     * @param ContainerInterface $container
     * @param string $configKey
     * @param string $section
     * @param string $factoryClassName
     * @return mixed
     */
    protected function retrieveDependency(ContainerInterface $container, $configKey, $section, $factoryClassName)
    {
        $containerKey = sprintf('doctrine.%s.%s', $section, $configKey);

        if ($container->has($containerKey)) {
            return $container->get($containerKey);
        }

        return (new $factoryClassName($configKey))->__invoke($container);
    }
}
