<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Doctrine;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class ConnectionFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'entity_manager') + [
            'connection' => $configKey,
            'configuration' => $configKey,
        ];

        return EntityManager::create(
            $container->get(sprintf('doctrine.connection.%s', $config['connection'])),
            $container->get(sprintf('doctrine.configuration.%s', $config['configuration']))
        );
    }
}
