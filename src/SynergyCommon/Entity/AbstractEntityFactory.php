<?php
namespace SynergyCommon\Entity;

use Interop\Container\ContainerInterface;
use SynergyCommon\Exception\InvalidEntityException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class AbstractEntityFactory
 * @package SynergyCommon\Entity
 */
class AbstractEntityFactory implements AbstractFactoryInterface
{

    protected $_configPrefix;

    public function __construct()
    {
        $this->_configPrefix = strtolower(__NAMESPACE__) . '\\';
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (substr($requestedName, 0, strlen($this->_configPrefix)) != $this->_configPrefix) {
            return false;
        }

        return true;
    }

    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractEntity
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
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
