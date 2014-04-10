<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\ApcCache;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineApcCacheFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $host = '';
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $serviceLocator->get('application')->getRequest();

        if ($request instanceof Request) {
            /** @var $event \Zend\Mvc\MvcEvent */
            $event = $serviceLocator->get('application')->getMvcEvent();
            if ($event && $rm = $event->getRouteMatch()) {
                $host = $rm->getParam('host');
            }
        } else {
            $host = $request->getServer('HTTP_HOST');
        }

        $prefix = preg_replace('/[^a-z0-9]/i', '', $host);

        $cache = new ApcCache();
        $cache->setNamespace($prefix);

        return $cache;
    }
}