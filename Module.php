<?php

namespace SynergyCommon;

use SynergyCommon\Controller\Plugin\SendPayload;
use SynergyCommon\Form\Element\GoogleCaptcha;
use SynergyCommon\Service\Factory\CacheStatusFactory;
use SynergyCommon\Service\Factory\DoctrineSessionSaveHandlerFactory;
use SynergyCommon\View\Helper\Factory\FlashMessagesHelperFactory;
use SynergyCommon\View\Helper\Factory\MicroDataHelperFactory;
use SynergyCommon\View\Helper\RenderGoogleCaptcha;

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
                'renderGoogleCaptcha' => RenderGoogleCaptcha::class,
            ),
            'factories'  => [
                'microData'     => MicroDataHelperFactory::class,
                'flashMessages' => FlashMessagesHelperFactory::class,
            ]
        ];
    }

    public function getFormElementConfig()
    {
        return array(
            'invokables' => array(
                'googleCaptcha' => GoogleCaptcha::class
            )
        );
    }
}
