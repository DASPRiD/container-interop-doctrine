<?php
use Aura\Di\Container;
use Aura\Di\Injection\InjectionFactory;
use Aura\Di\Resolver\Reflector;
use Aura\Di\Resolver\Resolver;

$container = new Container(new InjectionFactory(new Resolver(new Reflector())));

// Standard config keys
$container->set(
    'doctrine.entity_manager.orm_default',
    $container->lazy(new \ContainerInteropDoctrine\EntityManagerFactory(), $container)
);

// Custom config keys
$container->set(
    'doctrine.entity_manager.orm_other',
    $container->lazy(new \ContainerInteropDoctrine\EntityManagerFactory('orm_other'), $container)
);
