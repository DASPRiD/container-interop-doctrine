<?php
use PimpleInterop;

// Standard config keys
$container = new PimpleInterop(null, [
    'doctrine.connection.orm_default' => new \ContainerInteropDoctrine\ConnectionFactory(),
    'doctrine.configuration.orm_default' => new \ContainerInteropDoctrine\ConfigurationFactory(),
    'doctrine.entity_manager.orm_default' => new \ContainerInteropDoctrine\EntityManagerFactory(),
    'doctrine.event_manager.orm_default' => new \ContainerInteropDoctrine\EventManagerFactory(),
]);

// Custom config keys
$container = new PimpleInterop(null, [
    'doctrine.connection.orm_other' => new \ContainerInteropDoctrine\ConnectionFactory('orm_other'),
    'doctrine.configuration.orm_other' => new \ContainerInteropDoctrine\ConfigurationFactory('orm_other'),
    'doctrine.entity_manager.orm_other' => new \ContainerInteropDoctrine\EntityManagerFactory('orm_other'),
    'doctrine.event_manager.orm_other' => new \ContainerInteropDoctrine\EventManagerFactory('orm_other'),
]);
