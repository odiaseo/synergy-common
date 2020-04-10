<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Interop\Container\ContainerInterface;
use SynergyCommon\Exception\MemcacheNotAvailableException;
use Laminas\Console\Request;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DoctrineMemcacheFactory
 * @package SynergyCommon\Service
 */
class DoctrineMemcacheFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return ArrayCache|MemcacheCache|MemcachedCache
     * @throws MemcacheNotAvailableException
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $host = '';
        /** @var $request \Laminas\Http\PhpEnvironment\Request */
        $request = $serviceLocator->get('application')->getRequest();
        $status  = $serviceLocator->get('synergy\cache\status');
        $config  = $serviceLocator->get('Config');

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

            $prefix         = preg_replace('/[^a-z0-9]/i', '', $host);
            $memcacheConfig = $config['synergy']['memcache'];
            if (extension_loaded('memcached')) {
                /** @var MemcachedCache $cache */
                $cache    = new MemcachedCache();
                $memcache = new \Memcached();
                $cache->setMemcached($memcache);

                if (!$memcache->getServerList()) {
                    $memcache->addServer($memcacheConfig['host'], $memcacheConfig['port']);
                }
            } else {
                $cache    = new MemcacheCache();
                $memcache = new \Memcache();
                $cache->setMemcache($memcache);

                $connected = $memcache->connect($memcacheConfig['host'], $memcacheConfig['port']);
                if (!$connected) {
                    $exception = new MemcacheNotAvailableException(
                        'Cannot connect to server ' . $memcacheConfig['host'] . ':' . $memcacheConfig['port']
                    );
                    $serviceLocator->get('logger')->logException($exception);
                    throw $exception;
                }
            }

            $cache->setNamespace($prefix);
        } else {
            $cache = new ArrayCache();
        }

        return $cache;
    }
}
