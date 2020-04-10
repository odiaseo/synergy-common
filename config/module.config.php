<?php
use SynergyCommon\Service\ApiServiceFactory;
use SynergyCommon\Service\ServiceManagerAwareInitializer;

return array(
    'service_manager' => array(
        'abstract_factories' => array(
            'SynergyCommon\Model\AbstractModelFactory',
            'SynergyCommon\Service\AbstractServiceFactory',
            'SynergyCommon\Entity\AbstractEntityFactory',
        ),
        'invokables'         => array(
            'synergycommon\entity\licence'   => 'SynergyCommon\Entity\BaseLicence',
            'synergycommon\entity\site'      => 'SynergyCommon\Entity\BaseSite',
            'synergycommon\entity\session'   => 'SynergyCommon\Member\Entity\Session',
            'synergycommon\entity\userGroup' => 'SynergyCommon\Member\Entity\UserGroup',
        ),
        'factories'          => array(
            'logger'                              => 'SynergyCommon\Service\LoggerFactory',
            'common\api\service'                  => ApiServiceFactory::class,
            'doctrine.cache.synergy_memcache'     => 'SynergyCommon\Service\DoctrineMemcacheFactory',
            'doctrine.cache.synergy_apc'          => 'SynergyCommon\Service\DoctrineApcCacheFactory',
            'doctrine.cache.cache\factory'        => 'SynergyCommon\Service\DoctrineCacheFactory',
            'doctrine.cache.result\cache\factory' => 'SynergyCommon\Service\DoctrineResultCacheFactory',
            'Laminas\Session\Config\ConfigInterface' => 'Laminas\Session\Service\SessionConfigFactory',
        ),
        'delegators'         => [
            'translator'    => [
                SynergyCommon\Delegator\TranslatorDelegator::class,
            ],
            'MvcTranslator' => [
                SynergyCommon\Delegator\TranslatorDelegator::class,
            ],
        ],
        'initializers'       => [
            'injectContainer' => ServiceManagerAwareInitializer::class
        ],
        'shared' => [
            \SynergyCommon\Doctrine\CachedEntityManager::class => false
        ]
    ),
    'controllers'     => [
        'abstract_factories' => [
            \Laminas\Mvc\Controller\LazyControllerAbstractFactory::class,
        ],
    ],

    'session_config'  => [
        'phpSaveHandler'      => 'files',
        //'savePath'            => '/tmp/',
        'remember_me_seconds' => 7200,
        'cookie_httponly'     => true,
        'cookie_lifetime'     => 7200,
        'gc_maxlifetime'      => 7200,
    ],
    'session'         => [
        'config'     => [
            'class'   => \Laminas\Session\Config\SessionConfig::class,
            'options' => [
                'name' => 'synergy',
            ],
        ],
        'storage'    => \Laminas\Session\Storage\SessionArrayStorage::class,
        'validators' => [
            //\Laminas\Session\Validator\RemoteAddr::class,
            //\Laminas\Session\Validator\HttpUserAgent::class,
        ],
    ],
    'session_storage' => [
        'type' => \Laminas\Session\Storage\SessionArrayStorage::class,
    ],
    'synergy'         => array(
        'check_entity_cache_file' => true,
        'model_factory_prefix'    => 'am\model\\',
        'compress_output'         => true,
        'memcache'                => array(
            'host' => '127.0.0.1',
            'port' => 11211
        ),
        'api'                     => array(
            'options' => array(
                'headers' => array(
                    'User-Agent' => 'Synergy Afifiliate Platform v1.0',
                ),
            )
        ),
        'logger'                  => array(
            'priority' => \Laminas\Log\Logger::DEBUG
        ),
        'cache_control'           => 6
    ),
);
