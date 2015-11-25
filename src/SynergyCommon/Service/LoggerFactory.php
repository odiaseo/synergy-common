<?php
namespace SynergyCommon\Service;

use SynergyCommon\Util\ErrorHandler;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class LoggerFactory
 *
 * @package SynergyCommon\Service
 */
class LoggerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed|ErrorHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config    = $serviceLocator->get('config');
        $directory = 'data/logs/';
        $namespace = __NAMESPACE__;

        if (isset($config['synergy']['logger']['directory'])) {
            $directory = $config['synergy']['logger']['directory'];
        }

        if (isset($config['synergy']['logger']['namespace'])) {
            $namespace = $config['synergy']['logger']['namespace'];
        }

        $priority = $config['synergy']['logger']['priority'];
        $filename = rtrim($directory, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR . 'app.log';
        $handler  = new ErrorHandler();

        if (class_exists('Monolog\Logger')) {
            $stream = new  \Monolog\Handler\RotatingFileHandler($filename, 5, $priority, true, 0777);
            $logger = new \Monolog\Logger($namespace, array($stream));
        } else {
            $logger   = new Logger();
            $resource = fopen($filename, 'w');
            $writer   = new Stream($resource);
            $logger->addWriter($writer, $priority);
        }
        $handler->setServiceLocator($serviceLocator);
        $handler->setLogger($logger);

        return $handler;
    }
}
