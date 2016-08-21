<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Initializer\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

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
        if ($instance instanceof ServiceLocatorAwareInterface
            && !$instance instanceof AbstractPluginManager
        ) {
            $instance->setServiceLocator($container);
        }

        if ($instance instanceof ServiceLocatorAwareInterface
            && $instance instanceof AbstractPluginManager
            && !$instance->getServiceLocator()
        ) {
            $instance->setServiceLocator($container);
        }
    }
}
