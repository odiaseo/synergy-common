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
        ]
    ),
    'controllers'     => [
        'abstract_factories' => [
            \Zend\Mvc\Controller\LazyControllerAbstractFactory::class,
        ],
    ],
    'session'         => array(
        'config'       => array(
            'model'      => 'common\model\session',
            'class'      => 'Zend\Session\Config\SessionConfig',
            'options'    => array(
                'name'                => 'synergycommon',
                'remember_me_seconds' => 14400,
            ),
            'validators' => array(
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
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
        'model_factory_prefix' => 'am\model\\',
        'compress_output'      => true,
        'memcache'             => array(
            'host' => '127.0.0.1',
            'port' => 11211
        ),
        'api'                  => array(
            'options' => array(
                'headers' => array(
                    'User-Agent' => 'Synergy Afifiliate Platform v1.0',
                ),
            )
        ),
        'logger'               => array(
            'priority' => \Zend\Log\Logger::DEBUG
        )
    ),
);
