<?php
namespace SynergyCommon\Service\Factory;

use Interop\Container\ContainerInterface;
use SynergyCommon\Session\SaveHandler\DoctrineSaveHandler;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $config  = $serviceLocator->get('config');
        $model   = $serviceLocator->get($config['session']['config']['model']);
        $handler = new DoctrineSaveHandler($model);

        return $handler;
    }
}
