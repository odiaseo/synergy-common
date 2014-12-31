<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineApcCacheFactory
 *
 * @package SynergyCommon\Service
 */
class DoctrineApcCacheFactory implements FactoryInterface {
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
			$cache = new ApcCache();
		} else {
			$cache = new ArrayCache();
		}

		$cache->setNamespace( $prefix );

		return $cache;
	}
}
