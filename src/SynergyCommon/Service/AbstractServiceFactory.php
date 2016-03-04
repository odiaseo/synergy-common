<?php
namespace SynergyCommon\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractServiceFactory
    implements AbstractFactoryInterface
{

    protected $_configPrefix;

    public function __construct()
    {
        $this->_configPrefix = strtolower(__NAMESPACE__) . '\\';
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (substr($requestedName, 0, strlen($this->_configPrefix)) != $this->_configPrefix) {
            return false;
        }

        return true;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return AbstractService mixed
     * @throws \SynergyCommon\Exception\InvalidEntityException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $serviceId   = str_replace($this->_configPrefix, '', $requestedName);
        $serviceName = __NAMESPACE__ . '\\' . ucfirst($serviceId) . 'Service';

        if (class_exists($serviceName)) {
            /** @var $service \SynergyCommon\Service\AbstractService */
            $service = new $serviceName();
        } else {
            $service = new BaseService();
        }

        $service->setServiceManager($serviceLocator);
        $service->setEntityKey($serviceId);

        return $service;
    }
}