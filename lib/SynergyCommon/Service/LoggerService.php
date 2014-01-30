<?php
namespace SynergyCommon\Service;

use Monolog\Handler\RotatingFileHandler;
use SynergyCommon\Util\ErrorHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerService
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        if (isset($config['logger'])) {
            $directory = $config['logger']['directory'];
            $namespace = $config['logger']['namespace'];
        } else {
            $directory = 'data/logs/';
            $namespace = __NAMESPACE__;
        }
        $filename = rtrim($directory) . DIRECTORY_SEPARATOR . 'app.log';
        $stream   = new RotatingFileHandler($filename, 5);
        $logger   = new ErrorHandler($namespace, array($stream));

        return $logger;
    }
}