# container-interop-doctrine

[![Build Status](https://api.travis-ci.org/DASPRiD/container-interop-doctrine.png?branch=master)](http://travis-ci.org/DASPRiD/container-interop-doctrine)
[![Coverage Status](https://coveralls.io/repos/DASPRiD/container-interop-doctrine/badge.png?branch=master)](https://coveralls.io/r/DASPRiD/container-interop-doctrine)

[Doctrine](https://github.com/doctrine) factories for [container-interop](https://github.com/container-interop/container-interop)

This package provides a set of factories to be used with containers using the container-interop standard for an easy
Doctrine integration in a project.

## Installation

The easiest way to install this package is through composer:

```bash
$ composer require dasprid/container-interop-doctrine
```

## Configuration

In the general case where you are only using a single connection, it's enough to define the following factories in your
dependency container configuration:

```php
return [
    'dependencies' => [
        'factories' => [
            'doctrine.connection.orm_default' => \ContainerInteropDoctrine\ConnectionFactory::class,
            'doctrine.configuration.orm_default' => \ContainerInteropDoctrine\ConfigurationFactory::class,
            'doctrine.entity_manager.orm_default' => \ContainerInteropDoctrine\EntityManagerFactory::class,
            'doctrine.event_manager.orm_default' => \ContainerInteropDoctrine\EventManagerFactory::class,
        ],
    ],
];
```

If you want to add a second connection, or use another name than "orm_default", you can do so by using the static
variants of the factories:

```php
return [
    'dependencies' => [
        'factories' => [
            'doctrine.connection.orm_other' => [\ContainerInteropDoctrine\ConnectionFactory::class, 'orm_other'],
            'doctrine.configuration.orm_other' => [\ContainerInteropDoctrine\ConfigurationFactory::class, 'orm_other'],
            'doctrine.entity_manager.orm_other' => [\ContainerInteropDoctrine\EntityManagerFactory::class, 'orm_other'],
            'doctrine.event_manager.orm_other' => [\ContainerInteropDoctrine\EventManagerFactory::class, 'orm_other'],
        ],
    ],
];
```

For container specific configurations, there are a few examples provided in the example directory:

- [Aura.Di](example/aura-di.php)
- [PimpleInterop](example/pimple-interop.php)
- [Zend\ServiceManager](example/zend-servicemanager.php)

## Example configuration

A complete example configuration can be found in the [example/config.php](example/config.php). Please note that the
values in there are the defaults, and don't have to be supplied when you are not changing them. Keep your own
configuration as minimal as possible.