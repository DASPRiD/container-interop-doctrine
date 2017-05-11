<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrineTest\TestAsset;

use ContainerInteropDoctrine\AbstractFactory;
use Psr\Container\ContainerInterface;

class StubFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        return $configKey;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveConfig(ContainerInterface $container, $configKey, $section)
    {
        return parent::retrieveConfig($container, $configKey, $section);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig($configKey)
    {
        return [];
    }
}
