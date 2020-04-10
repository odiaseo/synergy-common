<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Interop\Container\ContainerInterface;
use Laminas\Console\Request;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DoctrineApcCacheFactory
 *
 * @package SynergyCommon\Service
 */
class DoctrineApcCacheFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return ApcuCache|ArrayCache
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $host = '';
        /** @var $request \Laminas\Http\PhpEnvironment\Request */
        $request = $serviceLocator->get('application')->getRequest();
        $status  = $serviceLocator->get('synergy\cache\status');

        if ($status->enabled) {
            if ($request instanceof Request) {
                /** @var $event \Laminas\Mvc\MvcEvent */
                $event = $serviceLocator->get('application')->getMvcEvent();
                if ($event and $routeMatch = $event->getRouteMatch()) {
                    $host = $routeMatch->getParam('host');
                }
            } else {
                $host = $request->getServer('HTTP_HOST');
            }

            $prefix = preg_replace('/[^a-z0-9]/i', '', $host);
            $cache  = new ApcuCache();
            $cache->setNamespace($prefix);
        } else {
            $cache = new ArrayCache();
        }

        return $cache;
    }
}
