<?php
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'url' => 'mysql://user:passeord@localhost/database',
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver::class,
                'drivers' => [
                    'My\Entity' => 'my_entity',
                ],
            ],
            'my_entity' => [
                'class' => \Doctrine\ORM\Mapping\Driver\XmlDriver::class,
                'paths' => __DIR__ . '/doctrine',
            ],
        ],
    ],
];
