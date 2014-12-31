<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcacheCache;
use SynergyCommon\Exception\MemcacheNotAvailableException;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineMemcacheFactory
	implements FactoryInterface {
	public function createService( ServiceLocatorInterface $serviceLocator ) {
		$host = '';
		/** @var $request \Zend\Http\PhpEnvironment\Request */
		$request = $serviceLocator->get( 'application' )->getRequest();
		$status  = $serviceLocator->get( 'synergy\cache\status' );

		if ( $request instanceof Request ) {
			/** @var $event \Zend\Mvc\MvcEvent */
			$event = $serviceLocator->get( 'application' )->getMvcEvent();
			if ( $event && $rm = $event->getRouteMatch() ) {
				$host = $rm->getParam( 'host' );
			}
		} else {
			$host = $request->getServer( 'HTTP_HOST' );
		}

		$prefix = preg_replace( '/[^a-z0-9]/i', '', $host );

		if ( $status->enabled ) {
			$cache          = new MemcacheCache();
			$memcache       = new \Memcache();
			$config         = $serviceLocator->get( 'config' );
			$memcacheConfig = $config['synergy']['memcache'];
			$connected      = $memcache->connect( $memcacheConfig['host'], $memcacheConfig['port'] );
			if ( ! $connected ) {
				throw new MemcacheNotAvailableException(
					'Cannot connect to server ' . $memcacheConfig['host'] . ':' . $memcacheConfig['port']
				);
			}
			$cache->setMemcache( $memcache );
		} else {
			$cache = new ArrayCache();
		}

		$cache->setNamespace( $prefix );

		return $cache;
	}
}
