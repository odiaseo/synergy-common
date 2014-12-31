<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SynergyCommon;

use Zend\Console\Request;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Module
 *
 * @package SynergyCommon
 */
class Module {

	/**
	 * @return array
	 */
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	/**
	 * @return array
	 */
	public function getServiceConfig() {

		return array(
			'factories' => array(
				'synergy\cache\status' => function ( $serviceLocator ) {
					/** @var $authService \Zend\Authentication\AuthenticationService */
					/** @var ServiceLocatorInterface $serviceLocator */
					$request = $serviceLocator->get( 'request' );

					if ( $serviceLocator->has( 'zfcuser_auth_service' ) ) {
						$authService = $serviceLocator->get( 'zfcuser_auth_service' );
						$identity    = $authService->hasIdentity();
					} else {
						$identity = false;
					}

					if ( $request instanceof Request ) {
						$enabled = false;
					} elseif ( $identity ) {
						$enabled = false;
					} elseif ( isset( $config['enable_result_cache'] ) ) {
						$enabled = $config['enable_result_cache'];
					} else {
						$enabled = false;
					}

					$return = array( 'enabled' => $enabled );

					return (object) $return;
				}
			)
		);
	}
}
