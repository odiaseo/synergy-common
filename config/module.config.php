<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonAffiliateManager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'service_manager' => array(
        'abstract_factories' => array(
            'SynergyCommon\Model\AbstractModelFactory',
            'SynergyCommon\Service\AbstractServiceFactory',
            'SynergyCommon\Entity\AbstractEntityFactory',
        ),
        'invokables'         => array(
            'synergycommon\entity\licence' => 'SynergyCommon\Entity\BaseLicence',
            'synergycommon\entity\site'    => 'SynergyCommon\Entity\BaseSite',
        ),
        'factories'          => array(
            'logger'                              => 'SynergyCommon\Service\LoggerFactory',
            'doctrine.cache.synergy_memcache'     => 'SynergyCommon\Service\DoctrineMemcacheFactory',
            'doctrine.cache.synergy_apc'          => 'SynergyCommon\Service\DoctrineApcCacheFactory',
            'doctrine.cache.cache\factory'        => 'SynergyCommon\Service\DoctrineCacheFactory',
            'doctrine.cache.result\cache\factory' => 'SynergyCommon\Service\DoctrineResultCacheFactory',
        )
    ),
    'session'         => array(
        'config'       => array(
            'model'   => 'common\model\session',
            'class'   => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'synergycommon',
            ),
        ),
        'save_handler' => array(
            'cache' => array(
                'adapter' => array(
                    'name'    => 'filesystem',
                    'options' => array(
                        'cache_dir' => getcwd() . '/data/session',
                    )
                )
            ),
        ),
        'lifetime'     => 7200,
        'storage'      => 'Zend\Session\Storage\SessionArrayStorage',
        'validators'   => array(),
    ),
    'synergy'         => array(
        'memcache' => array(
            'host' => '127.0.0.1',
            'port' => 11211
        ),
        'api'      => array(
            'options' => array(
                'headers' => array(
                    'User-Agent' => 'Synergy Afifiliate Platform v1.0',
                ),
            )
        ),
        'logger'   => array(
            'priority' => \Zend\Log\Logger::DEBUG
        )
    )
);
