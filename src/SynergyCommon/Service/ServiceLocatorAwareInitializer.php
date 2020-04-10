<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ServiceLocatorAwareInitializer
 * @package AffiliateManager\Service
 */
class ServiceLocatorAwareInitializer implements InitializerInterface
{

    /**
     * @param ContainerInterface $container
     * @param object $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($container instanceof ServiceManager && $instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceManager($container);
        }
    }
}
