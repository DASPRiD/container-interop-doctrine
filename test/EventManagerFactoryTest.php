<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrineTest;

use ContainerInteropDoctrine\EventManagerFactory;
use ContainerInteropDoctrine\Exception\DomainException;
use ContainerInteropDoctrine\Exception\InvalidArgumentException;
use ContainerInteropDoctrineTest\TestAsset\StubEventListener;
use ContainerInteropDoctrineTest\TestAsset\StubEventSubscriber;
use Doctrine\ORM\Events;
use Psr\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

class EventManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $factory = new EventManagerFactory();
        $eventManager = $factory($this->prophesize(ContainerInterface::class)->reveal());

        $this->assertSame(0, count($eventManager->getListeners()));
    }

    public function testInvalidInstanceSubscriber()
    {
        $factory = new EventManagerFactory();
        $this->setExpectedException(
            DomainException::class,
            'Invalid event subscriber "stdClass" given'
        );
        $factory($this->buildContainer(new stdClass())->reveal());
    }

    public function testInvalidTypeSubscriber()
    {
        $factory = new EventManagerFactory();
        $this->setExpectedException(
            DomainException::class,
            'Invalid event subscriber "integer" given'
        );
        $factory($this->buildContainer(1)->reveal());
    }

    public function testInvalidStringSubscriber()
    {
        $container = $this->buildContainer('NonExistentClass');
        $container->has('NonExistentClass')->willReturn(false);

        $factory = new EventManagerFactory();
        $this->setExpectedException(
            DomainException::class,
            'Invalid event subscriber "NonExistentClass" given'
        );
        $factory($container->reveal());
    }

    public function testInstanceSubscriber()
    {
        $factory = new EventManagerFactory();
        $eventManager = $factory($this->buildContainer(new StubEventSubscriber())->reveal());

        $this->assertSame(1, count($eventManager->getListeners('foo')));
    }

    public function testClassNameSubscriber()
    {
        $container = $this->buildContainer(StubEventSubscriber::class);
        $container->has(StubEventSubscriber::class)->willReturn(false);

        $factory = new EventManagerFactory();
        $eventManager = $factory($container->reveal());

        $this->assertSame(1, count($eventManager->getListeners('foo')));
    }

    public function testServiceNameSubscriber()
    {
        $eventSubscriber = new StubEventSubscriber();

        $container = $this->buildContainer(StubEventSubscriber::class);
        $container->has(StubEventSubscriber::class)->willReturn(true);
        $container->get(StubEventSubscriber::class)->willReturn($eventSubscriber);

        $factory = new EventManagerFactory();
        $eventManager = $factory($container->reveal());
        $listeners = $eventManager->getListeners('foo');

        $this->assertSame($eventSubscriber, array_pop($listeners));
    }

    public function testInvalidTypeListener()
    {
        $factory = new EventManagerFactory();
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Invalid event listener config: must be an array'
        );
        $factory($this->buildContainerWithListener(1)->reveal());
    }

    public function testInvalidStringListener()
    {
        $container = $this->buildContainerWithListener([ 'listener' => 'NonExistentClass']);
        $container->has('NonExistentClass')->willReturn(false);

        $factory = new EventManagerFactory();
        $this->setExpectedException(
            DomainException::class,
            'Invalid event listener "NonExistentClass" given'
        );
        $factory($container->reveal());
    }

    public function testInvalidEventNameListener()
    {
        $container = $this->buildContainerWithListener([
            'events' => [Events::onFlush, 'foo'],
            'listener' => new StubEventListener()
        ]);

        $factory = new EventManagerFactory();
        $this->setExpectedException(
            DomainException::class,
            sprintf(
                'Invalid event listener "%s" given: must have a "foo" method',
                StubEventListener::class
            )
        );
        $factory($container->reveal());
    }

    public function testInstanceListener()
    {
        $factory = new EventManagerFactory();
        $eventManager = $factory($this->buildContainerWithListener([
            'events' => Events::onFlush,
            'listener' => new StubEventListener()
        ])->reveal());

        $this->assertSame(1, count($eventManager->getListeners(Events::onFlush)));
    }

    public function testClassNameListener()
    {
        $container = $this->buildContainerWithListener([
            'events' => Events::onFlush,
            'listener' => StubEventListener::class
        ]);
        $container->has(StubEventListener::class)->willReturn(false);

        $factory = new EventManagerFactory();
        $eventManager = $factory($container->reveal());

        $this->assertSame(1, count($eventManager->getListeners(Events::onFlush)));
    }

    public function testServiceNameListener()
    {
        $eventListener = new StubEventListener();

        $container = $this->buildContainerWithListener([
            'events' => Events::onFlush,
            'listener' => StubEventListener::class
        ]);
        $container->has(StubEventListener::class)->willReturn(true);
        $container->get(StubEventListener::class)->willReturn($eventListener);

        $factory = new EventManagerFactory();
        $eventManager = $factory($container->reveal());
        $listeners = $eventManager->getListeners(Events::onFlush);

        $this->assertSame($eventListener, array_pop($listeners));
    }

    /**
     * @param mixed $subscriber
     * @return ContainerInterface|ObjectProphecy
     */
    private function buildContainer($subscriber)
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'doctrine' => [
                'event_manager' => [
                    'orm_default' => [
                        'subscribers' => [
                            $subscriber
                        ],
                    ],
                ],
            ],
        ]);

        return $container;
    }

    /**
     * @param mixed $listener
     * @return ContainerInterface|ObjectProphecy
     */
    private function buildContainerWithListener($listener)
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'doctrine' => [
                'event_manager' => [
                    'orm_default' => [
                        'listeners' => [
                            $listener
                        ],
                    ],
                ],
            ],
        ]);

        return $container;
    }
}
