<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'SynergyCommonTest\Controller\Test' => 'SynergyCommonTest\Controller\TestController',
        ),
    ),
    'router'      => array(
        'routes' => array(
            'api\home' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'SynergyCommonTest\Controller\Test',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'doctrine'    => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params'      => array(
                    'host'     => '127.0.0.1',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'affiliate',
                ),
            ),
        ),
    ),
);
