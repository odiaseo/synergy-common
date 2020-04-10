<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DoctrineCacheFactory
 *
 * @package AffiliateManager\Service
 */
class DoctrineCacheFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceManager
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $appEnv      = defined('APPLICATION_ENV') ? APPLICATION_ENV : 'production';
        $status      = $serviceManager->get('synergy\cache\status');
        $hasMemcache = extension_loaded('memcache') || extension_loaded('memcached');
        $hasApc      = extension_loaded('apcu') || extension_loaded('apc');

        /** @var $serviceManager \Laminas\ServiceManager\ServiceManager */
        if (!$status->enabled) {
            return $serviceManager->get('doctrine.cache.array');
        } elseif ($appEnv == 'production' and $hasApc) {
            return $serviceManager->get('doctrine.cache.synergy_apc');
        } elseif ($appEnv == 'production' and $hasMemcache) {
            return $serviceManager->get('doctrine.cache.synergy_memcache');
        } else {
            return $serviceManager->get('doctrine.cache.filesystem');
        }
    }
}
