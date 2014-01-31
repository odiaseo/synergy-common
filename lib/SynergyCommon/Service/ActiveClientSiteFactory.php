<?php
namespace Vaboose\Service;

use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class ActiveClientSiteFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $serviceLocator->get('application')->getRequest();

        if ($request instanceof Request) {
            /** @var $event \Zend\Mvc\MvcEvent */
            $event = $serviceLocator->get('application')->getMvcEvent();
            $rm    = $event->getRouteMatch();
            $host  = $rm->getParam('host');
        } else {
            $host = $request->getServer('HTTP_HOST');
        }
        $host = str_replace(array('http://', 'https://', 'www.'), '', $host);

        /** @var $container \ArrayObject */
        $container = new Container(__NAMESPACE__);

        if ($container->offsetExists('active-site')) {
            $site = $container->offsetGet('active-site');
        } elseif (!$site = $serviceLocator->get('synergycommon\service\api')->getSiteDetails($host)) {
            throw new \InvalidArgumentException("Site {$host} is not registered");
        } else {
            $site = new \ArrayObject($site);
            $container->offsetSet('active-site', $site);
        }

        return $site;
    }
}