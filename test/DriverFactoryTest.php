<?php
/**
 * @license See the file LICENSE for copying permission
 */

namespace ContainerInteropDoctrineTest;

use OutOfBoundsException;
use ContainerInteropDoctrine\DriverFactory;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;

class DriverFactoryTest extends TestCase
{
    public function testMissingClassKeyWillReturnOutOfBoundException()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $factory = new DriverFactory();

        $this->setExpectedException(OutOfBoundsException::class, 'Missing "class" config key');

        $factory($container->reveal());
    }

    public function testItSupportsGlobalBasenameOptionOnFileDrivers()
    {
        $globalBasename = 'foobar';

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'doctrine' => [
                'driver' => [
                    'orm_default' => [
                        'class' => TestAsset\StubFileDriver::class,
                        'global_basename' => $globalBasename
                    ],
                ],
            ],
        ]);

        $factory = new DriverFactory();

        $driver = $factory($container->reveal());
        $this->assertSame($globalBasename, $driver->getGlobalBasename());
    }
}
