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
use ContainerInteropDoctrineTest\TestAsset\StubEventSubscriber;
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
}
