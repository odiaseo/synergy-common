<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SynergyCommonTest;

use SynergyCommon\Event\Listener\SynergyModuleListener;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @package SynergyCommonTest
 */
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        /** @var $eventManager \Zend\EventManager\EventManager */
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(new SynergyModuleListener());
    }

    public function getConfig()
    {
        return include __DIR__ . '/../test.config.php';
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

    public function getServiceConfig()
    {
        return array(
            'aliases'   => array(
                'session_manager' => 'Zend\Session\SessionManager'
            ),
            'factories' => array(
                'Zend\Session\SessionManager' => 'SynergyCommon\Session\SessionManager',
                'synergycommon\service\api'   => 'SynergyCommon\Service\ApiServiceFactory',
                'logger'                      => 'SynergyCommon\Service\LoggerFactory',
                'active\site'                 => 'SynergyCommon\Service\ActiveClientSiteFactory',
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'flashMessages' => 'SynergyCommon\View\Helper\FlashMessages',
            )
        );
    }
}
