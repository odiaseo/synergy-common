<?php
namespace SynergyCommon\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineMemcacheFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $site \SynergyCommon\Entity\AbstractEntity */
        $site     = $serviceLocator->get('active_site');
        $cache    = new MemcacheService();
        $memcache = new \Memcache();

        $memcache->connect('localhost', 11211);
        $cache->setMemcache($memcache);
        $cache->setSite($site);

        return $cache;
    }
}