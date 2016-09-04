<?php

namespace SynergyCommon;

use SynergyCommon\Controller\Plugin\SendPayload;
use SynergyCommon\Factory\LazyInvokableFactory;
use SynergyCommon\Form\Element\GoogleCaptcha;
use SynergyCommon\Service\Factory\CacheStatusFactory;
use SynergyCommon\Service\Factory\DoctrineSessionSaveHandlerFactory;
use SynergyCommon\Session\SessionManagerFactory;
use SynergyCommon\View\Helper\Factory\FlashMessagesHelperFactory;
use SynergyCommon\View\Helper\Factory\MicroDataHelperFactory;
use SynergyCommon\View\Helper\RenderGoogleCaptcha;
use Zend\Mvc\ModuleRouteListener;
use Zend\Session\SessionManager;

/**
 * Class Module
 *
 * @package SynergyCommon
 */
class Module
{
    public function onBootstrap($e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

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
                SessionManager::class           => SessionManagerFactory::class,
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
            'aliases'   => array(
                'GoogleCaptcha' => GoogleCaptcha::class
            ),
            'factories' => [
                GoogleCaptcha::class => LazyInvokableFactory::class
            ],
        );
    }
}
