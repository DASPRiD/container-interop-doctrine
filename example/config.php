<?php
return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'proxy_dir' => 'data/cache/DoctrineEntityProxy',
                'proxy_namespace' => 'DoctrineEntityProxy',
                'auto_generate_proxy_classes' => false,
                'use_underscore_naming_strategy' => false,
            ],
        ],
        'connection' => [
            'orm_default' => [
                'driver_class' => \Doctrine\DBAL\Driver\PDOMySql\Driver::class,
                'wrapper_class' => null,
                'pdo' => null,
                'configuration' => 'orm_default', // Actually defaults to the connection config key, not hard-coded
                'event_manager' => 'orm_default', // Actually defaults to the connection config key, not hard-coded
                'params' => [],
                'doctirne_mapping_types' => [],
                'doctrine_commented_types' => [],
            ],
        ],
        'entity_manager' => [
            'orm_default' => [
                'connection' => 'orm_default', // Actually defaults to the connection config key, not hard-coded
                'configuration' => 'orm_default', // Actually defaults to the connection config key, not hard-coded
            ],
        ],
        'event_manager' => [
            'orm_default' => [
                'subscribers' => [],
            ],
        ],
    ],
];