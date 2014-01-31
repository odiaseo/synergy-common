<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\MemcacheCache;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineMemcacheFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cache    = new MemcacheCache();
        $memcache = new \Memcache();
        $memcache->connect('localhost', 11211);
        $cache->setMemcache($memcache);

        return $cache;
    }
}