<?php

namespace SynergyCommon;

use SynergyCommon\Controller\Plugin\SendPayload;
use SynergyCommon\Service\Factory\CacheStatusFactory;
use SynergyCommon\Service\Factory\DoctrineSessionSaveHandlerFactory;

/**
 * Class Module
 *
 * @package SynergyCommon
 */
class Module
{

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {

        return array(
            'factories' => array(
                'common\model\session'          => 'SynergyCommon\Model\SessionModelFactory',
                'session\doctrine\save\handler' => DoctrineSessionSaveHandlerFactory::class,
                'session\memcache\save\handler' => DoctrineSessionSaveHandlerFactory::class,
                'synergy\cache\status'          => CacheStatusFactory::class,
            )
        );
    }

    public function getControllerPluginConfig()
    {
        return [
            'invokables' => [
                'sendPayload' => SendPayload::class,
            ]
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => array(
                'renderGoogleCaptcha' => 'SynergyCommon\View\Helper\RenderGoogleCaptcha',
            ),
        ];
    }

    public function getFormElementConfig()
    {
        return array(
            'invokables' => array(
                'GoogleCaptcha' => 'SynergyCommon\Form\Element\GoogleCaptcha'
            )
        );
    }
}
