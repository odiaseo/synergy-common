<?php

namespace SynergyCommon\Factory;

use Interop\Container\ContainerInterface;
use SynergyCommon\Service\ServiceLocatorAwareInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class LazyInvokableFactory
 * @package SynergyCommon\Factory
 */
class LazyInvokableFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $object = (null === $options) ? new $requestedName : new $requestedName($options);
        if ($object instanceof ServiceLocatorAwareInterface) {
            $object->setServiceLocator($container);
        }

        return $object;
    }
}
