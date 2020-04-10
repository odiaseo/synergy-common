<?php
namespace SynergyCommon\Service\Factory;

use Interop\Container\ContainerInterface;
use SynergyCommon\Session\SaveHandler\DoctrineSaveHandler;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineSessionSaveHandlerFactory
 * @package SynergyCommon\Service\Factory
 */
class DoctrineSessionSaveHandlerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return DoctrineSaveHandler
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var ServiceLocatorInterface $serviceLocator */
        $config  = $serviceLocator->get('Config');
        $model   = $serviceLocator->get($config['session']['config']['model']);
        $handler = new DoctrineSaveHandler($model);

        return $handler;
    }
}
