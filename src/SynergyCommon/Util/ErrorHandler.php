<?php
namespace SynergyCommon\Util;

use SynergyCommon\Service\ServiceLocatorAwareInterface;
use SynergyCommon\Service\ServiceLocatorAwareTrait;
use SynergyCommon\Util;
use Zend\Http\PhpEnvironment\Request;
use Zend\Log\Logger;

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

    /** @var  Logger */
    private $loggerWithName;

    public function logException(\Exception $e)
    {
        $request = $this->getServiceLocator()->get('request');
        if ($request instanceof Request) {
            $uri = $request->getUriString() . ' : ';
        } else {
            $uri = '';
        }
        $log = $uri . $this->processException($e);
        if ($logger = $this->getLoggerWithName()) {
            /** @var  $logger \Zend\Log\LoggerInterface */
            $logger->err($log);
        }

        return $this;
    }

    public static function logCacheException(\Exception $exception)
    {
        $data     = self::processException($exception);
        $filename = sprintf('data/logs/cache-exception-%s.txt', date('Y-m-d'));
        file_put_contents($filename, $data . PHP_EOL, FILE_APPEND);
    }

    public function log($priority, $message, $extra = [])
    {
        $request = $this->getServiceLocator()->get('request');
        if ($request instanceof Request) {
            $extra[] = 'uri: ' . $request->getUriString();
        }

        return $this->getLoggerWithName()->log($priority, $message, $extra);
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
        return Util::customCall($this->getLoggerWithName(), $method, $args);
    }

    /**
     * @param \Zend\Log\LoggerInterface | \Psr\Log\LoggerInterface $logger
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
        $this->getLoggerWithName()->warn('Proxy not found', $args);
    }

    private function getLoggerWithName()
    {
        if (!$this->loggerWithName) {
            $namespace = '';
            if ($this->logger instanceof \Monolog\Logger and $this->getServiceLocator()->has('active\site')) {
                $site      = $this->getServiceLocator()->get('active\site');
                $namespace = Util::urlize($site->getDomain());
            }

            if ($namespace) {
                $this->loggerWithName = $this->logger->withName($namespace);
            } else {
                return $this->logger;
            }
        }

        return $this->loggerWithName;
    }
}
