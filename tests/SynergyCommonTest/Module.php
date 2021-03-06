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
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;

/**
 * Class Module
 * @package SynergyCommonTest
 */
class Module
{
    public function onBootstrap(MvcEvent $event)
    {
        /** @var $eventManager \Laminas\EventManager\EventManager */
        $eventManager        = $event->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        /** @var $serviceLocator \Laminas\ServiceManager\ServiceManager */
        $serviceLocator  = $event->getApplication()->getServiceManager();
        $synergyListener = new SynergyModuleListener();
        $synergyListener->attach($eventManager);

        $synergyListener->initSession($event);
        $synergyListener->bootstrap($eventManager, $serviceLocator);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/test.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'logger'      => 'SynergyCommon\Service\LoggerFactory',
                'active\site' => 'SynergyCommon\Service\ActiveClientSiteFactory',
            ),
        );
    }
}
