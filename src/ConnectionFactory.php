<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Doctrine;

use Doctrine\DBAL\Driver\PDOMySql\Driver as PdoMysqlDriver;
use Doctrine\DBAL\DriverManager;
use Interop\Container\ContainerInterface;

class ConnectionFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'connection') + [
            'driver_class' => PdoMysqlDriver::class,
            'wrapper_class' => null,
            'pdo' => null,
            'configuration' => $configKey,
            'event_manager' => $configKey,
            'params' => [],
            'doctirne_mapping_types' => [],
            'doctrine_commented_types' => [],
        ];

        $params = $config['params'] + [
            'driverClass' => $config['driver_class'],
            'wrapperClass' => $config['wrapper_class'],
            'pdo' => $config['pdo'],
        ];

        $connection = DriverManager::getConnection(
            $params,
            $container->get(sprintf('doctrine.configuration.%s', $config['configuration'])),
            $container->get(sprintf('doctrine.event_manager.%s', $config['configuration']))
        );

        $platform = $connection->getDatabasePlatform();

        foreach ($connection['doctrine_mapping_types'] as $dbType => $doctrineType) {
            $platform->registerDoctrineTypeMapping($dbType, $doctrineType);
        }

        foreach ($connection['doctrine_commented_types'] as $doctrineType) {
            $platform->markDoctrineTypeCommented($doctrineType);
        }

        return $connection;
    }
}
