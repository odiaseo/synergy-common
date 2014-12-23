<?php
namespace SynergyCommon\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineCacheFactory
 *
 * @package AffiliateManager\Service
 */
class DoctrineCacheFactory implements FactoryInterface {
	/**
	 * Generate Stoker cache storage
	 *
	 * @param ServiceLocatorInterface $serviceManager
	 *
	 * @return mixed|void
	 */
	public function createService( ServiceLocatorInterface $serviceManager ) {
		$appEnv = defined( 'APPLICATION_ENV' ) ? APPLICATION_ENV : 'production';
		/** @var $serviceManager \Zend\ServiceManager\ServiceManager */
		if ( php_sapi_name() == 'cli' ) {
			return $serviceManager->get( 'doctrine.cache.array' );
		} elseif ( $appEnv == 'production' || extension_loaded( 'memcache' ) ) {
			return $serviceManager->get( 'doctrine.cache.synergy_memcache' );
		} elseif ( $appEnv == 'production' && extension_loaded( 'apc' ) ) {
			return $serviceManager->get( 'doctrine.cache.synergy_apc' );
		} else {
			return $serviceManager->get( 'doctrine.cache.array' );
		}
	}
}
