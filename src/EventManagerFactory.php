<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrine;

use ContainerInteropDoctrine\Exception\DomainException;
use ContainerInteropDoctrine\Exception\InvalidArgumentException;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Psr\Container\ContainerInterface;

/**
 * @method EventManager __invoke(ContainerInterface $container)
 */
class EventManagerFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'event_manager');
        $eventManager = new EventManager();

        foreach ($config['subscribers'] as $subscriber) {
            if (is_object($subscriber)) {
                $subscriberName = get_class($subscriber);
            } elseif (!is_string($subscriber)) {
                $subscriberName = gettype($subscriber);
            } elseif ($container->has($subscriber)) {
                $subscriber = $container->get($subscriber);
                $subscriberName = $subscriber;
            } elseif (class_exists($subscriber)) {
                $subscriber = new $subscriber();
                $subscriberName = get_class($subscriber);
            } else {
                $subscriberName = $subscriber;
            }

            if (!$subscriber instanceof EventSubscriber) {
                throw new DomainException(sprintf(
                    'Invalid event subscriber "%s" given, mut be a dependency name, class name or an instance'
                    . ' implementing %s',
                    $subscriberName,
                    EventSubscriber::class
                ));
            }

            $eventManager->addEventSubscriber($subscriber);
        }

        foreach ($config['listeners'] as $listenerConfig) {
            if (!is_array($listenerConfig)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid event listener config: must be an array, "%s" given',
                    gettype($listenerConfig)
                ));
            }

            $listener = $listenerConfig['listener'];
            $listenerName = $listener;

            if (is_object($listener)) {
                $listenerName = get_class($listener);
            } elseif (!is_string($listener)) {
                $listenerName = gettype($listener);
            } elseif ($container->has($listener)) {
                $listener = $container->get($listener);
            } elseif (class_exists($listener)) {
                $listener = new $listener();
                $listenerName = get_class($listener);
            }

            if (!is_object($listener)) {
                throw new DomainException(sprintf(
                    'Invalid event listener "%s" given, must be a dependency name, class name or an object',
                    $listenerName
                ));
            }

            foreach ((array) $listenerConfig['events'] as $event) {
                if (!method_exists($listener, $event)) {
                    throw new DomainException(sprintf(
                        'Invalid event listener "%s" given: must have a "%s" method',
                        $listenerName,
                        $event
                    ));
                }
            }

            $eventManager->addEventListener($listenerConfig['events'], $listener);
        }

        return $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig($configKey)
    {
        return [
            'subscribers' => [],
            'listeners' => [],
        ];
    }
}
