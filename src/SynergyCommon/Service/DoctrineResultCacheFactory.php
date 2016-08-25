<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Zend\Cache\Storage\StorageInterface;

/**
 * Class DoctrineCacheFactory
 *
 * @package AffiliateManager\Service
 */
class DoctrineResultCacheFactory extends DoctrineCacheFactory
{
    /**
     * Generate Stoker cache storage
     *
     * @param ContainerInterface $serviceManager
     *
     * @return mixed|void
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        /** @var StorageInterface $cache */
        $cache  = parent::__invoke($serviceManager, $requestedName, $options);
        $status = $serviceManager->get('synergy\cache\status');
        if ($status->enabled) {
            $cache->getOptions()->setTtl(7200);
        }

        return $cache;
    }
}
