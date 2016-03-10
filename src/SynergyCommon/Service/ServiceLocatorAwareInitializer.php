<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Class ServiceLocatorAwareInitializer
 * @package AffiliateManager\Service
 */
class ServiceLocatorAwareInitializer implements InitializerInterface
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

        if ($container instanceof ServiceManager && $instance instanceof ServiceManagerAwareInterface) {
            $instance->setServiceManager($container);
        }
    }
}
