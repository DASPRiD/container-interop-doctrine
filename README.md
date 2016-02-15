# container-interop-doctrine

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
        ],
    ],
];
```
