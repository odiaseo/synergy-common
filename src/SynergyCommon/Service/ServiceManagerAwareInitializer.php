<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Class ServiceManagerAwareInitializer
 * @package AffiliateManager\Service
 */
class ServiceManagerAwareInitializer implements InitializerInterface
{

    /**
     * Initialize
     *
     * @param $first
     * @param ServiceLocatorInterface $second
     * @return mixed
     */
    public function initialize($first, ServiceLocatorInterface $second)
    {
        if ($first instanceof ContainerInterface) {
            $container = $first;
            $instance  = $second;
        } else {
            $container = $second;
            $instance  = $first;
        }

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
