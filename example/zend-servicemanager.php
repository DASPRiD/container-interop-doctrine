<?php
use Zend\ServiceManage\ServiceManager;

// Standard config keys
$container = new ServiceManager([
    'factories' => [
        'doctrine.connection.orm_default' => \ContainerInteropDoctrine\ConnectionFactory::class,
        'doctrine.configuration.orm_default' => \ContainerInteropDoctrine\ConfigurationFactory::class,
        'doctrine.entity_manager.orm_default' => \ContainerInteropDoctrine\EntityManagerFactory::class,
        'doctrine.event_manager.orm_default' => \ContainerInteropDoctrine\EventManagerFactory::class,
    ],
]);

// Custom config keys
$container = new ServiceManager([
    'factories' => [
        'doctrine.connection.orm_other' => [\ContainerInteropDoctrine\ConnectionFactory::class, 'orm_other'],
        'doctrine.configuration.orm_other' => [\ContainerInteropDoctrine\ConfigurationFactory::class, 'orm_other'],
        'doctrine.entity_manager.orm_other' => [\ContainerInteropDoctrine\EntityManagerFactory::class, 'orm_other'],
        'doctrine.event_manager.orm_other' => [\ContainerInteropDoctrine\EventManagerFactory::class, 'orm_other'],
    ],
]);
