<?php
use Aura\Di\Container;
use Aura\Di\Injection\InjectionFactory;
use Aura\Di\Resolver\Reflector;
use Aura\Di\Resolver\Resolver;

$container = new Container(new InjectionFactory(new Resolver(new Reflector())));

// Standard config keys
$container->set(
    'doctrine.connection.orm_default',
    $container->lazy(new \ContainerInteropDoctrine\ConnectionFactory(), $container)
);
$container->set(
    'doctrine.configuration.orm_default',
    $container->lazy(new \ContainerInteropDoctrine\ConfigurationFactory(), $container)
);
$container->set(
    'doctrine.entity_manager.orm_default',
    $container->lazy(new \ContainerInteropDoctrine\EntityManagerFactory(), $container)
);
$container->set(
    'doctrine.event_manager.orm_default',
    $container->lazy(new \ContainerInteropDoctrine\EventManagerFactory(), $container)
);

// Custom config keys
$container->set(
    'doctrine.connection.orm_other',
    $container->lazy(new \ContainerInteropDoctrine\ConnectionFactory('orm_other'), $container)
);
$container->set(
    'doctrine.configuration.orm_other',
    $container->lazy(new \ContainerInteropDoctrine\ConfigurationFactory('orm_other'), $container)
);
$container->set(
    'doctrine.entity_manager.orm_other',
    $container->lazy(new \ContainerInteropDoctrine\EntityManagerFactory('orm_other'), $container)
);
$container->set(
    'doctrine.event_manager.orm_other',
    $container->lazy(new \ContainerInteropDoctrine\EventManagerFactory('orm_other'), $container)
);
