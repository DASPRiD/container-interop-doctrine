<?php
return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'result_cache' => 'array',
                'metadata_cache' => 'array',
                'query_cache' => 'array',
                'hydration_cache' => 'array',
                'driver' => 'orm_default', // Actually defaults to the configuration config key, not hard-coded
                'auto_generate_proxy_classes' => true,
                'proxy_dir' => 'data/cache/DoctrineEntityProxy',
                'proxy_namespace' => 'DoctrineEntityProxy',
                'entity_namespaces' => [],
                'datetime_functions' => [],
                'string_functions' => [],
                'numeric_functions' => [],
                'filters' => [],
                'named_queries' => [],
                'named_native_queries' => [],
                'custom_hydration_modes' => [],
                'naming_strategy' => null,
                'default_repository_class_name' => null,
                'repository_factory' => null,
                'class_metadata_factory_name' => null,
                'entity_listener_resolver' => null,
                'second_level_cache' => [
                    'enabled' => false,
                    'default_lifetime' => 3600,
                    'default_lock_lifetime' => 60,
                    'file_lock_region_directory' => '',
                    'regions' => [],
                ],
                'sql_logger' => null,
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
                'doctrine_mapping_types' => [],
                'doctrine_commented_types' => [],
            ],
        ],
        'entity_manager' => [
            'orm_default' => [
                'connection' => 'orm_default', // Actually defaults to the entity manager config key, not hard-coded
                'configuration' => 'orm_default', // Actually defaults to the entity manager config key, not hard-coded
            ],
        ],
        'event_manager' => [
            'orm_default' => [
                'subscribers' => [],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => null,
                'paths' => [],
                'extension' => null,
                'drivers' => [],
                'global_basename' => null,
            ]
        ],
        'cache' => [
            'apcu' => [
                'class' => \Doctrine\Common\Cache\ApcuCache::class,
                'namespace' => 'container-interop-doctrine',
            ],
            'array' => [
                'class' => \Doctrine\Common\Cache\ArrayCache::class,
                'namespace' => 'container-interop-doctrine',
            ],
            'filesystem' => [
                'class' => \Doctrine\Common\Cache\FilesystemCache::class,
                'directory' => 'data/cache/DoctrineCache',
                'namespace' => 'container-interop-doctrine',
            ],
            'memcache' => [
                'class' => \Doctrine\Common\Cache\MemcacheCache::class,
                'instance' => 'my_memcache_alias',
                'namespace' => 'container-interop-doctrine',
            ],
            'memcached' => [
                'class' => \Doctrine\Common\Cache\MemcachedCache::class,
                'instance' => 'my_memcached_alias',
                'namespace' => 'container-interop-doctrine',
            ],
            'predis' => [
                'class' => \Doctrine\Common\Cache\PredisCache::class,
                'instance' => 'my_predis_alias',
                'namespace' => 'container-interop-doctrine',
            ],
            'redis' => [
                'class' => \Doctrine\Common\Cache\RedisCache::class,
                'instance' => 'my_redis_alias',
                'namespace' => 'container-interop-doctrine',
            ],
            'wincache' => [
                'class' => \Doctrine\Common\Cache\WinCacheCache::class,
                'namespace' => 'container-interop-doctrine',
            ],
            'xcache' => [
                'class' => \Doctrine\Common\Cache\XcacheCache::class,
                'namespace' => 'container-interop-doctrine',
            ],
            'zenddata' => [
                'class' => \Doctrine\Common\Cache\ZendDataCache::class,
                'namespace' => 'container-interop-doctrine',
            ],
            'chain' => [
                'class' => \Doctrine\Common\Cache\ChainCache::class,
                'providers' => ['array', 'redis'], // you can use any provider listed above
                'namespace' => 'container-interop-doctrine', // will be applied to all providers in the chain
            ],
        ],
        'types' => [],
    ],
];
