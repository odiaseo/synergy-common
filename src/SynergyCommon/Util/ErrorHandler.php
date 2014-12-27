<?php
namespace SynergyCommon\Util;


use SynergyCommon\Util;

class ErrorHandler {

	private $_logger;

	public function logException( \Exception $e ) {
		$log = $this->processException( $e );
		if ( $logger = $this->getLogger() ) {
			/** @var  $logger \Zend\Log\LoggerInterface */
			$logger->err( $log );
		}

		return $this;
	}


	/**
	 * Format exception
	 *
	 * @param \Exception $e
	 *
	 * @return string
	 */
	public static function processException( \Exception $e ) {
		$trace = $e->getTraceAsString();
		$i     = 1;
		do {
			$messages[] = $i ++ . ": " . $e->getMessage();
		} while ( $e = $e->getPrevious() );

		$log = "Exception:\n" . implode( "\n", $messages );
		$log .= "\nTrace:\n" . $trace . "\n\n";

		return $log;
	}

	/**
	 * Proxy Method to logger
	 *
	 * @param       $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args = array() ) {
		return Util::customCall( $this->_logger, $method, $args );
	}

	/**
	 * @param \Zend\Log\LoggerInterface $logger
	 */
	public function setLogger( $logger ) {
		$this->_logger = $logger;
	}

	/**
	 * @return \Zend\Log\LoggerInterface
	 */
	public function getLogger() {
		return $this->_logger;
	}

	/**
	 * @param $proxy
	 * @param $namespace
	 * @param $className
	 */
	public function logProxyNotFound( $proxy, $namespace, $className ) {
		$args = func_get_args();
		$this->getLogger()->warn( 'Proxy not found', $args );
	}
}
