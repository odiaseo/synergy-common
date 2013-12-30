<?php
namespace SynergyCommon\Entity;

use SynergyCommon\Exception\InvalidEntityException;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractEntityFactory
    implements AbstractFactoryInterface
{

    protected $_configPrefix;

    public function __construct()
    {
        $this->_configPrefix = 'synergycommon\entity\\';
    }

    /**
     * Determine if we can create a entity with name
     *
     * @param ServiceLocatorInterface $entityLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $entityLocator, $name, $requestedName)
    {
        if (substr($requestedName, 0, strlen($this->_configPrefix)) != $this->_configPrefix) {
            return false;
        }

        return true;
    }

    /**
     * Crete entity with name
     *
     * @param ServiceLocatorInterface $entityLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return \SynergyCommon\Entity\AbstractEntity
     * @throws \SynergyCommon\Exception\InvalidEntityException
     */
    public function createServiceWithName(ServiceLocatorInterface $entityLocator, $name, $requestedName)
    {
        $entityId   = str_replace($this->_configPrefix, '', $requestedName);
        $entityName = __NAMESPACE__ . '\\' . ucfirst($entityId);

        if (class_exists($entityName)) {
            /** @var $entity \SynergyCommon\Entity\AbstractEntity */
            $entity = new $entityName();

            return $entity;
        } else {
            throw new InvalidEntityException('Invalid entity: ' . $entityName);
        }

    }
}