<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;

/**
 * Interface ServiceLocatorAwareInterface
 * @package SynergyCommon\Service
 */
interface ServiceLocatorAwareInterface
{
    /**
     * @return ContainerInterface
     */
    public function getServiceLocator();

    /**
     * @param ContainerInterface $serviceLocator
     */
    public function setServiceLocator(ContainerInterface $serviceLocator);

}
