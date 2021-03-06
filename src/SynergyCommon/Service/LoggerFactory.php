<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use SynergyCommon\Util\ErrorHandler;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class LoggerFactory
 *
 * @package SynergyCommon\Service
 */
class LoggerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return ErrorHandler
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config    = $serviceLocator->get('Config');
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

        $stream = new  RotatingFileHandler($filename, 5, $priority, true, 0777);
        $logger = new Logger($namespace, array($stream));

        $handler->setServiceLocator($serviceLocator);
        $handler->setLogger($logger);

        return $handler;
    }
}
