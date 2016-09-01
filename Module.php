<?php

namespace SynergyCommon;

use SynergyCommon\Controller\Plugin\SendPayload;
use SynergyCommon\Factory\LazyInvokableFactory;
use SynergyCommon\Form\Element\GoogleCaptcha;
use SynergyCommon\Service\Factory\CacheStatusFactory;
use SynergyCommon\Service\Factory\DoctrineSessionSaveHandlerFactory;
use SynergyCommon\View\Helper\Factory\FlashMessagesHelperFactory;
use SynergyCommon\View\Helper\Factory\MicroDataHelperFactory;
use SynergyCommon\View\Helper\RenderGoogleCaptcha;
use Zend\Mvc\ModuleRouteListener;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
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
                SessionManager::class           => function ($container) {
                    $config = $container->get('config');
                    if (!isset($config['session'])) {
                        $sessionManager = new SessionManager();
                        Container::setDefaultManager($sessionManager);
                        return $sessionManager;
                    }

                    $session = $config['session'];

                    $sessionConfig = null;
                    if (isset($session['config'])) {
                        $class = isset($session['config']['class'])
                            ? $session['config']['class']
                            : SessionConfig::class;

                        $options = isset($session['config']['options'])
                            ? $session['config']['options']
                            : [];

                        $sessionConfig = new $class();
                        $sessionConfig->setOptions($options);
                    }

                    $sessionStorage = null;
                    if (isset($session['storage'])) {
                        $class          = $session['storage'];
                        $sessionStorage = new $class();
                    }

                    $sessionSaveHandler = null;
                    if (isset($session['save_handler'])) {
                        // class should be fetched from service manager
                        // since it will require constructor arguments
                        $sessionSaveHandler = $container->get($session['save_handler']);
                    }

                    $sessionManager = new SessionManager(
                        $sessionConfig,
                        $sessionStorage,
                        $sessionSaveHandler
                    );

                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
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
