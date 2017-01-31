<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Proxy\Exception\OutOfBoundsException;
use Interop\Container\ContainerInterface;

/**
 * @method MappingDriver __invoke(ContainerInterface $container)
 */
class DriverFactory extends AbstractFactory
{
    /**
     * @var bool
     */
    private static $isAnnotationLoaderRegistered = false;

    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'driver');

        if (!array_key_exists('class', $config)) {
            throw new OutOfBoundsException('Missing "class" config key');
        }

        if (!is_array($config['paths'])) {
            $config['paths'] = [$config['paths']];
        }

        if (AnnotationDriver::class === $config['class'] || is_subclass_of($config['class'], AnnotationDriver::class)) {
            $this->registerAnnotationLoader();

            $driver = new $config['class'](
                new CachedReader(
                    new AnnotationReader(),
                    $this->retrieveDependency($container, $config['cache'], 'cache', CacheFactory::class)
                ),
                $config['paths']
            );
        } else {
            $driver = new $config['class']($config['paths']);
        }

        if (null !== $config['extension'] && $driver instanceof FileDriver) {
            $locator = $driver->getLocator();

            if (get_class($locator) !== DefaultFileLocator::class) {
                throw new Exception\DomainException(sprintf(
                    'File locator must be a concrete instance of %s, got %s',
                    DefaultFileLocator::class,
                    get_class($locator)
                ));
            }

            $driver->setLocator(new DefaultFileLocator($locator->getPaths(), $config['extension']));
        }

        if (isset($config['global_basename']) && $driver instanceof FileDriver) {
            $driver->setGlobalBasename($config['global_basename']);
        }

        if ($driver instanceof MappingDriverChain) {
            foreach ($config['drivers'] as $namespace => $driverName) {
                if (null === $driverName) {
                    continue;
                }

                $driver->addDriver($this->createWithConfig($container, $driverName), $namespace);
            }
        }

        return $driver;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig($configKey)
    {
        return [
            'paths' => [],
            'extension' => null,
            'drivers' => [],
        ];
    }

    /**
     * Registers the annotation loader
     */
    private function registerAnnotationLoader()
    {
        if (self::$isAnnotationLoaderRegistered) {
            return;
        }

        AnnotationRegistry::registerLoader(
            function ($className) {
                return class_exists($className);
            }
        );

        self::$isAnnotationLoaderRegistered = true;
    }
}
