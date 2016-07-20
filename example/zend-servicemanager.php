<?php
use Zend\ServiceManager\ServiceManager;

// Standard config keys
$container = new ServiceManager([
    'factories' => [
        'doctrine.entity_manager.orm_default' => \ContainerInteropDoctrine\EntityManagerFactory::class,
    ],
]);

// Custom config keys
$container = new ServiceManager([
    'factories' => [
        'doctrine.entity_manager.orm_other' => [\ContainerInteropDoctrine\EntityManagerFactory::class, 'orm_other'],
    ],
]);
