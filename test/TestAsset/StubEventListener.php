<?php
/**
 * @license See the file LICENSE for copying permission
 */

namespace ContainerInteropDoctrineTest\TestAsset;

use Doctrine\ORM\Event\OnFlushEventArgs;

class StubEventListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
    }
}
