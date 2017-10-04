<?php
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'url' => 'mysql://user:password@localhost/database',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain::class,
                'drivers' => [
                    'My\Entity' => 'my_entity',
                ],
            ],
            'my_entity' => [
                'class' => \Doctrine\ORM\Mapping\Driver\XmlDriver::class,
                'cache' => 'array',
                'paths' => __DIR__ . '/doctrine',
            ],
        ],
    ],
];

/**
* switch out the user and password with the correct connection string
* note that the my_entity driver you specified  is looking for entities written in xml files
* for entities written in php use the Annotation Driver (see full config)
*/
