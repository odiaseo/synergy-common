<?php
namespace SynergyCommon\Service;

use SynergyCommon\Util\ErrorHandler;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        if (isset($config['synergy']['logger'])) {
            $directory = $config['synergy']['logger']['directory'];
            $namespace = $config['synergy']['logger']['namespace'];
        } else {
            $directory = 'data/logs/';
            $namespace = __NAMESPACE__;
        }

        $filename = rtrim($directory, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR . 'app.log';
        $handler  = new ErrorHandler();

        if (class_exists('Monolog\Logger')) {
            $stream = new  \Monolog\Handler\RotatingFileHandler($filename, 5);
            $logger = new \Monolog\Logger($namespace, array($stream));
        } else {
            $logger   = new Logger();
            $resource = fopen($filename, 'w');
            $writer   = new Stream($resource);
            $logger->addWriter($writer);
        }

        $handler->setLogger($logger);

        return $handler;
    }
}