<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Initializer\InitializerInterface;

/**
 * Class ServiceManagerAwareInitializer
 * @package AffiliateManager\Service
 */
class ServiceManagerAwareInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param object $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($container);
        } elseif (method_exists($instance, 'setServiceManager')) {
            $instance->setServiceManager($container);
        }
    }
}
