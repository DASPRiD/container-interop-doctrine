<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrineTest;

use ContainerInteropDoctrine\Exception\DomainException;
use ContainerInteropDoctrineTest\TestAsset\StubFactory;
use Psr\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;

class AbstractFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultConfigKey()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new StubFactory();
        $this->assertSame('orm_default', $factory($container));
    }

    public function testCustomConfigKey()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new StubFactory('orm_other');
        $this->assertSame('orm_other', $factory($container));
    }

    public function testStaticCall()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $this->assertSame('orm_other', StubFactory::orm_other($container));
    }

    public function testStaticCallWithoutContainer()
    {
        $this->setExpectedException(
            DomainException::class,
            'The first argument must be of type Psr\Container\ContainerInterface'
        );
        StubFactory::orm_other();
    }

    /**
     * @dataProvider configProvider
     * @param string $configKey
     * @param string $section
     * @param array $expectedResult
     * @param array|null $config
     */
    public function testRetrieveConfig($configKey, $section, array $expectedResult, array $config = null)
    {
        $container = $this->prophesize(ContainerInterface::class);

        if (null === $config) {
            $container->has('config')->willReturn(false);
        } else {
            $container->has('config')->willReturn(true);
            $container->get('config')->willReturn($config);
        }

        $factory = new StubFactory();
        $result = $factory->retrieveConfig($container->reveal(), $configKey, $section);

        $this->assertSame($expectedResult, $result);
    }

    public function configProvider()
    {
        return [
            'no-config' => ['foo', 'bar', [], null],
            'doctrine-missing' => ['foo', 'bar', [], []],
            'section-missing' => ['foo', 'bar', [], ['doctrine' => []]],
            'config-key-missing' => ['foo', 'bar', [], ['doctrine' => ['bar' => []]]],
            'config-key-exists' => ['foo', 'bar', [1], ['doctrine' => ['bar' => ['foo' => [1]]]]],
        ];
    }
}
