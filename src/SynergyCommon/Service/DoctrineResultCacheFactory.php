<?php
namespace SynergyCommon\Service;

use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineCacheFactory
 *
 * @package AffiliateManager\Service
 */
class DoctrineResultCacheFactory extends DoctrineCacheFactory {
	/**
	 * Generate Stoker cache storage
	 *
	 * @param ServiceLocatorInterface $serviceManager
	 *
	 * @return mixed|void
	 */
	public function createService( ServiceLocatorInterface $serviceManager ) {
		/** @var StorageInterface $cache */
		$cache  = parent::createService( $serviceManager );
		$status = $serviceManager->get( 'synergy\cache\status' );
		if ( $status->enabled ) {
			$cache->getOptions()->setTtl( 7200 );
		}

		return $cache;
	}
}
