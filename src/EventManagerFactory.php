<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrine;

use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Interop\Container\ContainerInterface;

class EventManagerFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'event_manager') + [
            'subscribers' => [],
        ];

        $eventManager = new EventManager();

        foreach ($config['subscribers'] as $subscriber) {
            if ($container->has($subscriber)) {
                $subscriber = $container->get($subscriber);
                $name = $subscriber;
            } elseif (class_exists($subscriber)) {
                $subscriber = new $subscriber();
                $name = get_class($subscriber);
            } elseif (is_object($subscriber)) {
                $name = get_class($subscriber);
            } else {
                $name = (string) $name;
            }

            if (!$subscriber instanceof EventSubscriber) {
                throw new Exception\DomainException(sprintf(
                    'Invalid event subscriber "%s" given, mut be a dependency name, class name or an instance'
                    . ' implementing %s',
                    $name,
                    EventSubscriber::class
                ));
            }

            $eventManager->addEventSubscriber($subscriber);
        }

        return $eventManager;
    }
}
