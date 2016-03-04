<?php
namespace SynergyCommon\Service\Factory;

use SynergyCommon\Session\SaveHandler\DoctrineSaveHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineSessionSaveHandlerFactory
 * @package SynergyCommon\Service\Factory
 */
class DoctrineSessionSaveHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ServiceLocatorInterface $serviceLocator */
        $config  = $serviceLocator->get('config');
        $model   = $serviceLocator->get($config['session']['config']['model']);
        $handler = new DoctrineSaveHandler($model);

        return $handler;
    }
}
