<?php
/**
 * @license See the file LICENSE for copying permission
 */

declare(strict_types = 1);

namespace ContainerInteropDoctrineTest\TestAsset;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;

class StubFileDriver extends FileDriver
{
    protected function loadMappingFile($file)
    {
        return [];
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
    }
}
