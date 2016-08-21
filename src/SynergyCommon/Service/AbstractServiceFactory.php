<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class AbstractServiceFactory
 * @package SynergyCommon\Service
 */
class AbstractServiceFactory implements AbstractFactoryInterface
{

    protected $_configPrefix;

    public function __construct()
    {
        $this->_configPrefix = strtolower(__NAMESPACE__) . '\\';
    }

    /**
     * Determine if we can create a service with name
     *
     * /**
     * @param ContainerInterface $serviceLocator
     * @param $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $serviceLocator, $requestedName)
    {
        if (substr($requestedName, 0, strlen($this->_configPrefix)) != $this->_configPrefix) {
            return false;
        }

        return true;
    }

    /**
     * Create service with name
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractService|BaseService
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
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
