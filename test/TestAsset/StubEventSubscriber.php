<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace ContainerInteropDoctrineTest\TestAsset;

use Doctrine\Common\EventSubscriber;

class StubEventSubscriber implements EventSubscriber
{
    /**
     * {q@nheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['foo'];
    }
}
