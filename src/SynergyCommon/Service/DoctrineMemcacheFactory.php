<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use SynergyCommon\Exception\MemcacheNotAvailableException;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineMemcacheFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $host = '';
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $serviceLocator->get('application')->getRequest();
        $status  = $serviceLocator->get('synergy\cache\status');
        if ($status->enabled) {
            if ($request instanceof Request) {
                /** @var $event \Zend\Mvc\MvcEvent */
                $event = $serviceLocator->get('application')->getMvcEvent();
                if ($event and $rm = $event->getRouteMatch()) {
                    $host = $rm->getParam('host');
                }
            } else {
                $host = $request->getServer('HTTP_HOST');
            }

            $prefix = preg_replace('/[^a-z0-9]/i', '', $host);

            if (extension_loaded('memcached')) {
                /** @var MemcachedCache $cache */
                $cache    = new MemcachedCache();
                $memcache = new \Memcached();
                $cache->setMemcached($memcache);
            } else {
                $cache    = new MemcacheCache();
                $memcache = new \Memcache();
                $cache->setMemcache($memcache);
            }

            if (!$memcache->getServerList()) {
                $config         = $serviceLocator->get('config');
                $memcacheConfig = $config['synergy']['memcache'];
                $memcache->addserver($memcacheConfig['host'], $memcacheConfig['port']);
            }
            /* $connected      = $memcache->connect($memcacheConfig['host'], $memcacheConfig['port']);
             if (!$connected) {
                 $exception = new MemcacheNotAvailableException(
                     'Cannot connect to server ' . $memcacheConfig['host'] . ':' . $memcacheConfig['port']
                 );
                 $serviceLocator->get('logger')->logException($exception);
                 throw $exception;
             }*/

            $cache->setNamespace($prefix);
        } else {
            $cache = new ArrayCache();
        }

        return $cache;
    }
}
