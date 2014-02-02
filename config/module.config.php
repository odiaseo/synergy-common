<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonAffiliateManager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'doctrine'        => array(
        'configuration' => array(
            'orm_default' => array(
                'driver'           => 'orm_default',
                'generate_proxies' => false,
                'proxy_dir'        => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'  => 'DoctrineORMModule\Proxy',
            )
        ),

        'driver'        => array(
            'synergy\common\admin'         => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../vendor/synergy/common/lib/SynergyCommon/Admin/Entity'
                )
            ),
            'synergy\common\entities'      => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../vendor/synergy/common/lib/SynergyCommon/Entity')
            ),
            'synergy\member\entities'      => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../vendor/synergy/common/lib/SynergyCommon/Member/Entity')
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
                    'Gedmo\Translatable\Entity' => 'translatable\metadata\driver',
                    'SynergyCommon\Entity'      => 'synergy\common\entities',
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
    'service_manager' => array(
        'abstract_factories' => array(
            'SynergyCommon\Model\AbstractModelFactory',
            'SynergyCommon\Service\AbstractServiceFactory',
            'SynergyCommon\Entity\AbstractEntityFactory',
        ),
        'invokables'         => array(
            'synergycommon\entity\licence' => 'SynergyCommon\Entity\BaseLicence',
            'synergycommon\entity\site'    => 'SynergyCommon\Entity\BaseSite',
        )
    ),
    'session'           => array(
        'config'       => array(
            'class'   => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'affiliatemanager',
            ),
        ),
        'save_handler' => array(
            'cache' => array(
                'adapter' => array(
                    'name'    => 'filesystem',
                    'options' => array(
                        'cache_dir' => __DIR__ . '/../data/session',
                    )
                )
            ),
        ),
        'lifetime'     => 7200,
        'storage'      => 'Zend\Session\Storage\SessionArrayStorage',
        'validators'   => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent',
        ),
    ),
);
