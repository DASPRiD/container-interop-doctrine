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
     * @var bool
     */
    private static $areTypesRegistered = false;

    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $this->registerTypes($container);

        $config = $this->retrieveConfig($container, $configKey, 'entity_manager');

        return EntityManager::create(
            $this->retrieveDependency(
                $container,
                $config['connection'],
                'connection',
                ConfigurationFactory::class
            ),
            $this->retrieveDependency(
                $container,
                $config['configuration'],
                'configuration',
                ConfigurationFactory::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig($configKey)
    {
        return [
            'connection' => $configKey,
            'configuration' => $configKey,
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

        foreach ($typesConfig as $name => $className) {
            if (Type::hasType($name)) {
                Type::overrideType($name, $className);
                continue;
            }

            Type::addType($name, $className);
        }

        self::$areTypesRegistered = true;
    }
}
