<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrineTest;

use ContainerInteropDoctrine\AbstractFactory;
use ContainerInteropDoctrine\CacheFactory;
use Doctrine\Common\Cache\FilesystemCache;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;

class CacheFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testExtendsAbstractFactory()
    {
        $this->assertInstanceOf(AbstractFactory::class, new CacheFactory());
    }

    public function testFileSystemCacheConstructor()
    {

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['doctrine'=>['cache'=>['filesystem'=>['class' => FilesystemCache::class,'directory'=>'test']]]]);


        $factory = new CacheFactory('filesystem');
        $cacheInstance = $factory($container->reveal());

        $this->assertInstanceOf(FilesystemCache::class, $cacheInstance);
    }

}
