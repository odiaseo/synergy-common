<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineCacheFactory
 *
 * @package AffiliateManager\Service
 */
class DoctrineCacheFactory implements FactoryInterface
{
    /**
     * Generate Stoker cache storage
     *
     * @param ServiceLocatorInterface $serviceManager
     *
     * @return mixed|void
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $appEnv      = defined('APPLICATION_ENV') ? APPLICATION_ENV : 'production';
        $status      = $serviceManager->get('synergy\cache\status');
        $hasMemcache = (extension_loaded('memcache') or extension_loaded('memcached'));

        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        if (!$status->enabled) {
            return $serviceManager->get('doctrine.cache.array');
        } elseif ($appEnv == 'production' and $hasMemcache) {
            return $serviceManager->get('doctrine.cache.synergy_memcache');
        } elseif ($appEnv == 'production' and extension_loaded('apc')) {
            return $serviceManager->get('doctrine.cache.synergy_apc');
        } else {
            return $serviceManager->get('doctrine.cache.filesystem');
        }
    }
}
