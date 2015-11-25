<?php
namespace SynergyCommon\Util;

use SynergyCommon\Util;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Request;
use Zend\Log\Logger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class ErrorHandler
 *
 * @package SynergyCommon\Util
 */
class ErrorHandler implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;
    /** @var  Logger */
    private $logger;

    public function logException(\Exception $e)
    {
        $request = $this->getServiceLocator()->get('request');
        if ($request instanceof Request) {
            $uri = $request->getUriString() . ' : ';
        } else {
            $uri = '';
        }
        $log = $uri . $this->processException($e);
        if ($logger = $this->getLogger()) {
            /** @var  $logger \Zend\Log\LoggerInterface */
            $logger->err($log);
        }

        return $this;
    }

    public function log($priority, $message, $extra = [])
    {
        $request = $this->getServiceLocator()->get('request');
        if ($request instanceof Request) {
            $extra[] = 'uri: ' . $request->getUriString();
        }

        return $this->logger->log($priority, $message, $extra);
    }

    /**
     * Format exception
     *
     * @param \Exception $e
     *
     * @return string
     */
    public static function processException(\Exception $e)
    {
        $trace = $e->getTraceAsString();
        $i     = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = "Exception:\n" . implode("\n", $messages);
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
    public function __call($method, $args = array())
    {
        return Util::customCall($this->logger, $method, $args);
    }

    /**
     * @param \Zend\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Zend\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param $proxy
     * @param $namespace
     * @param $className
     */
    public function logProxyNotFound($proxy, $namespace, $className)
    {
        $args = func_get_args();
        $this->getLogger()->warn('Proxy not found', $args);
    }
}
