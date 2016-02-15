# zend-expressive-doctrine

Doctrine subcomponent for [Expressive](https://github.com/zendframework/zend-expressive).

This package provides a set of factories to be used with expressive for an easy Doctrine integration.

## Installation

The easiest way to install this package is through composer:

```bash
$ composer require zendframework/zend-expressive-doctrine
```

## Configuration

In the general case where you are only using a single connection, it's enough to define the following factories in your
dependency container configuration:

```php
return [
    'dependencies' => [
        'factories' => [
            'doctrine.connection.orm_default' => \Zend\Expressive\Doctrine\ConnectionFactory::class,
            'doctrine.configuration.orm_default' => \Zend\Expressive\Doctrine\ConfigurationFactory::class,
            'doctrine.entity_manager.orm_default' => \Zend\Expressive\Doctrine\EntityManagerFactory::class,
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
            'doctrine.connection.orm_other' => [\Zend\Expressive\Doctrine\ConnectionFactory::class, 'orm_other'],
            'doctrine.configuration.orm_other' => [\Zend\Expressive\Doctrine\ConfigurationFactory::class, 'orm_other'],
            'doctrine.entity_manager.orm_other' => [\Zend\Expressive\Doctrine\EntityManagerFactory::class, 'orm_other'],
        ],
    ],
];
```
