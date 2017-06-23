<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrineTest;

use ContainerInteropDoctrine\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDOSqlite\Driver as PDOSqliteDriver;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Psr\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class ConnectionFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EventManager
     */
    private $eventManger;

    public function setUp()
    {
        $this->configuration = $this->prophesize(Configuration::class)->reveal();
        $this->eventManger = $this->prophesize(EventManager::class)->reveal();
    }

    public function testDefaultsThroughException()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM is somewhat funky here');
        }

        $factory = new ConnectionFactory();
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);
        $container->has('doctrine.configuration.orm_default')->willReturn(true);
        $container->get('doctrine.configuration.orm_default')->willReturn($this->configuration);
        $container->has('doctrine.event_manager.orm_default')->willReturn(true);
        $container->get('doctrine.event_manager.orm_default')->willReturn($this->eventManger);

        // This is actually quite tricky. We cannot really test the pure defaults, as that would require a MySQL
        // connection without a username and password. Since that can't work, we just verify that we get an exception
        // with the right backtrace, and test the other defaults with a pure memory-database later.
        try {
            $factory($container->reveal());
        } catch (ConnectionException $e) {
            $this->assertContains("Access denied for user ''@'localhost' (using password: NO)", $e->getMessage());

            foreach ($e->getTrace() as $entry) {
                if ('Doctrine\DBAL\Driver\PDOMySql\Driver' === $entry['class']) {
                    return;
                }
            }

            $this->fail('Exception was not raised by PDOMySql');
            return;
        }

        $this->fail('An expected exception was not raised');
    }

    public function testDefaults()
    {
        $factory = new ConnectionFactory();
        $connection = $factory($this->buildContainer()->reveal());

        $this->assertSame($this->configuration, $connection->getConfiguration());
        $this->assertSame($this->eventManger, $connection->getEventManager());
        $this->assertSame([
            'driverClass' => PDOSqliteDriver::class,
            'wrapperClass' => null,
            'pdo' => null,
        ], $connection->getParams());
    }

    public function testConfigKeysTakenFromSelf()
    {
        $factory = new ConnectionFactory('orm_other');
        $connection = $factory($this->buildContainer('orm_other', 'orm_other', 'orm_other')->reveal());

        $this->assertSame($this->configuration, $connection->getConfiguration());
        $this->assertSame($this->eventManger, $connection->getEventManager());
    }

    public function testConfigKeysTakenFromConfig()
    {
        $factory = new ConnectionFactory('orm_other');
        $connection = $factory($this->buildContainer('orm_other', 'orm_foo', 'orm_bar', [
            'configuration' => 'orm_foo',
            'event_manager' => 'orm_bar',
        ])->reveal());

        $this->assertSame($this->configuration, $connection->getConfiguration());
        $this->assertSame($this->eventManger, $connection->getEventManager());
    }

    public function testParamsInjection()
    {
        $factory = new ConnectionFactory();
        $connection = $factory($this->buildContainer('orm_default', 'orm_default', 'orm_default', [
            'params' => ['username' => 'foo'],
        ])->reveal());

        $this->assertSame([
            'username' => 'foo',
            'driverClass' => PDOSqliteDriver::class,
            'wrapperClass' => null,
            'pdo' => null,
        ], $connection->getParams());
    }

    public function testDoctrineMappingTypesInjection()
    {
        $factory = new ConnectionFactory();
        $connection = $factory($this->buildContainer('orm_default', 'orm_default', 'orm_default', [
            'doctrine_mapping_types' => ['foo' => 'boolean'],
        ])->reveal());

        $this->assertTrue($connection->getDatabasePlatform()->hasDoctrineTypeMappingFor('foo'));
    }

    public function testDoctrineCommentedTypesInjection()
    {
        $type = Type::getType('boolean');

        $factory = new ConnectionFactory();
        $connection = $factory($this->buildContainer('orm_default', 'orm_default', 'orm_default', [
            'doctrine_commented_types' => [$type],
        ])->reveal());

        $this->assertTrue($connection->getDatabasePlatform()->isCommentedDoctrineType($type));
    }

    public function testCustomTypeDoctrineMappingTypesInjection()
    {
        $factory = new ConnectionFactory();
        $property = (new \ReflectionObject($factory))->getProperty('areTypesRegistered');
        $property->setAccessible(true);
        $property->setValue($factory, false);

        $connection = $factory($this->buildContainer('orm_default', 'orm_default', 'orm_default', [
            'doctrine_mapping_types' => ['foo' => 'custom_type'],
        ])->reveal());

        $this->assertTrue($connection->getDatabasePlatform()->hasDoctrineTypeMappingFor('foo'));
    }
    /**
     * @param string $ownKey
     * @param string $configurationKey
     * @param string $eventManagerKey
     * @param array $config
     * @return ContainerInterface|ObjectProphecy
     */
    private function buildContainer(
        $ownKey = 'orm_default',
        $configurationKey = 'orm_default',
        $eventManagerKey = 'orm_default',
        array $config = []
    ) {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'doctrine' => [
                'connection' => [
                    $ownKey => $config + [
                        'driver_class' => PDOSqliteDriver::class,
                    ],
                ],
                'types' => [
                    'custom_type' => BooleanType::class
                ]
            ],
        ]);

        $container->has(sprintf('doctrine.configuration.%s', $configurationKey))->willReturn(true);
        $container->get(sprintf('doctrine.configuration.%s', $configurationKey))->willReturn($this->configuration);
        $container->has(sprintf('doctrine.event_manager.%s', $eventManagerKey))->willReturn(true);
        $container->get(sprintf('doctrine.event_manager.%s', $eventManagerKey))->willReturn($this->eventManger);

        return $container;
    }
}
