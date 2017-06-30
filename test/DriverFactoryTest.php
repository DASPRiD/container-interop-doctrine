<?php
/**
 * @license See the file LICENSE for copying permission
 */

namespace ContainerInteropDoctrineTest;

use Doctrine\ORM\Mapping\Driver;
use OutOfBoundsException;
use ContainerInteropDoctrine\DriverFactory;
use Psr\Container\ContainerInterface;
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

    /**
     * @param string $driverClass
     *
     * @dataProvider simplifiedDriverClassProvider
     */
    public function testItSupportsSettingExtensionInDriversUsingSymfonyFileLocator($driverClass)
    {
        $extension = '.foo.bar';

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'doctrine' => [
                'driver' => [
                    'orm_default' => [
                        'class' => $driverClass,
                        'extension' => $extension,
                    ],
                ],
            ],
        ]);

        $factory = new DriverFactory();

        /** @var Driver\SimplifiedXmlDriver $driver */
        $driver = $factory($container->reveal());
        $this->assertInstanceOf($driverClass, $driver);
        $this->assertSame($extension, $driver->getLocator()->getFileExtension());
    }

    public function simplifiedDriverClassProvider()
    {
        return [
            [ Driver\SimplifiedXmlDriver::class ],
            [ Driver\SimplifiedYamlDriver::class ],
        ];
    }
}
