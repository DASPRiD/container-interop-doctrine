<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver as PdoMysqlDriver;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;
use Doctrine\DBAL\Types\Type;

/**
 * @method Connection __invoke(ContainerInterface $container)
 */
class ConnectionFactory extends AbstractFactory
{
    /**
     * @var bool
     */
    private static $areTypesRegistered = false;

    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $this->registerTypes($container);

        $config = $this->retrieveConfig($container, $configKey, 'connection');
        $params = $config['params'] + [
            'driverClass' => $config['driver_class'],
            'wrapperClass' => $config['wrapper_class'],
            'pdo' => is_string($config['pdo']) ? $container->get($config['pdo']) : $config['pdo'],
        ];

        $connection = DriverManager::getConnection(
            $params,
            $this->retrieveDependency(
                $container,
                $config['configuration'],
                'configuration',
                ConfigurationFactory::class
            ),
            $this->retrieveDependency(
                $container,
                $config['event_manager'],
                'event_manager',
                EventManagerFactory::class
            )
        );
        $platform = $connection->getDatabasePlatform();

        foreach ($config['doctrine_mapping_types'] as $dbType => $doctrineType) {
            $platform->registerDoctrineTypeMapping($dbType, $doctrineType);
        }

        foreach ($config['doctrine_commented_types'] as $doctrineType) {
            $platform->markDoctrineTypeCommented($doctrineType);
        }

        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig($configKey)
    {
        return [
            'driver_class' => PdoMysqlDriver::class,
            'wrapper_class' => null,
            'pdo' => null,
            'configuration' => $configKey,
            'event_manager' => $configKey,
            'params' => [],
            'doctrine_mapping_types' => [],
            'doctrine_commented_types' => [],
        ];
    }

    /**
     * Registers all declared typed, if not already done.
     *
     * @param ContainerInterface $container
     */
    private function registerTypes(ContainerInterface $container)
    {
        if (self::$areTypesRegistered) {
            return;
        }

        $applicationConfig = $container->has('config') ? $container->get('config') : [];
        $doctrineConfig = array_key_exists('doctrine', $applicationConfig) ? $applicationConfig['doctrine'] : [];
        $typesConfig = array_key_exists('types', $doctrineConfig) ? $doctrineConfig['types'] : [];
        self::$areTypesRegistered = true;

        foreach ($typesConfig as $name => $className) {
            if (Type::hasType($name)) {
                Type::overrideType($name, $className);
                continue;
            }

            Type::addType($name, $className);
        }
    }
}
