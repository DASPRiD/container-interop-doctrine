<?php
use PimpleInterop;

// Standard config keys
$container = new PimpleInterop(null, [
    'doctrine.entity_manager.orm_default' => new \ContainerInteropDoctrine\EntityManagerFactory(),
]);

// Custom config keys
$container = new PimpleInterop(null, [
    'doctrine.entity_manager.orm_other' => new \ContainerInteropDoctrine\EntityManagerFactory('orm_other'),
]);
