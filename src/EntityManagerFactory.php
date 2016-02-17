<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrine;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

/**
 * @method EntityManager __invoke(ContainerInterface $container)
 */
class EntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
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
