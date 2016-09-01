<?php
return array(
    'router'       => array(
        'routes' => array(
            'api\home'  => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/test[/:id]',
                    'defaults' => array(
                        'controller' => 'SynergyCommonTest\Sample',
                    ),
                ),
            ),
            'test\page' => [
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/test-page',
                    'defaults' => array(
                        'controller' => 'SynergyCommonTest\Sample',
                        'action'     => 'index',
                    ),
                ),
            ]
        ),
    ),

    'doctrine'     => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params'      => array(
                    'driver'   => 'pdo_sqlite',
                    'host'     => '127.0.0.1',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'password',
                    'dbname'   => 'test',
                    'path'     => sys_get_temp_dir() . '/sqlite.db',
                ),
            )
        ),
        'configuration' => array(
            'orm_default' => array(
                'driver'            => 'orm_default',
                'generate_proxies'  => false,
                'proxy_dir'         => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'   => 'DoctrineORMModule\Proxy',
                'numeric_functions' => array(
                    'Rand' => 'SynergyCommon\Doctrine\Extension\Rand'
                )
            )
        ),
        'driver'        => array(
            'synergy\common\entities'      => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array('src/SynergyCommon/Entity')
            ),
            'synergy\member\entities'      => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array('src/SynergyCommon/Member/Entity')
            ),
            'translatable\metadata\driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    'vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity',
                ),
            ),
            'orm_default'                  => array(
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    'Gedmo\Translatable\Entity'   => 'translatable\metadata\driver',
                    'SynergyCommon\Entity'        => 'synergy\common\entities',
                    'SynergyCommon\Member\Entity' => 'synergy\member\entities',
                )
            )
        ),
        'eventmanager'  => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Tree\TreeListener',
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                    'Gedmo\Loggable\LoggableListener',
                    'Gedmo\Translatable\TranslatableListener'
                )
            ),
        ),
    ),
    'view_manager' => [
        'template_map'        => [
            'sample-view' => __DIR__ . '/../sample-view.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../',
        ],
    ],
    'synergy'      => [
        'model_factory_prefix' => 'common\model\\',
    ]
);
