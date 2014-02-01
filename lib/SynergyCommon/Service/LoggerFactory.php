<?php
namespace SynergyCommon\Service;

use Monolog\Handler\RotatingFileHandler;
use SynergyCommon\Util\ErrorHandler;
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
        $filename = rtrim($directory) . DIRECTORY_SEPARATOR . 'app.log';
        $stream   = new RotatingFileHandler($filename, 5);
        $logger   = new ErrorHandler($namespace, array($stream));

        return $logger;
    }
}