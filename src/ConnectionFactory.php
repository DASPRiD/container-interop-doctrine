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
use Interop\Container\ContainerInterface;

/**
 * @method Connection __invoke(ContainerInterface $container)
 */
class ConnectionFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'connection') + [
            'driver_class' => PdoMysqlDriver::class,
            'wrapper_class' => null,
            'pdo' => null,
            'configuration' => $configKey,
            'event_manager' => $configKey,
            'params' => [],
            'doctrine_mapping_types' => [],
            'doctrine_commented_types' => [],
        ];

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
}
