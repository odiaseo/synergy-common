<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Zend\Console\Request;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ActiveClientSiteFactory
 * @package SynergyCommon\Service
 */
class ActiveClientSiteFactory implements FactoryInterface
{
    const STIE_KEY = 'active-site';

    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $serviceLocator->get('application')->getRequest();

        if ($request instanceof Request) {
            /** @var $event \Zend\Mvc\MvcEvent */
            $event      = $serviceLocator->get('application')->getMvcEvent();
            $routeMatch = $event->getRouteMatch();
            $host       = $routeMatch->getParam('host');
        } else {
            $host = $request->getServer('HTTP_HOST');
        }
        $host = str_replace(array('http://', 'https://', 'www.'), '', $host);

        if (!$site = $serviceLocator->get('common\api\service')->getSiteDetails($host)) {
            throw new \InvalidArgumentException("Site {$host} is not registered");
        }

        return $site;
    }
}
