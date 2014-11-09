<?php
namespace SynergyCommon\Service;

use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ActiveClientSiteFactory
    implements FactoryInterface
{
    const STIE_KEY = 'active-site';

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

        if (!$site = $serviceLocator->get('synergycommon\service\api')->getSiteDetails($host)) {
            throw new \InvalidArgumentException("Site {$host} is not registered");
        }

        return $site;
    }
}