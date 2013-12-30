<?php
namespace SynergyCommon\Model;


use SynergyCommon\Exception\InvalidEntityException;
use Zend\Log\Formatter\Base;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractModelFactory
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
     * @return AbstractModel|mixed
     * @throws \SynergyCommon\Exception\InvalidEntityException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $modelId   = str_replace($this->_configPrefix, '', $requestedName);
        $modelName = __NAMESPACE__ . '\Model\\' . ucfirst($modelId) . 'Model';

        /** @var $model \SynergyCommon\Model\AbstractModel */
        $model = new $modelName();

        $entity          = $serviceLocator->get('am\entity\\' . $modelId);
        $entityClassname = get_class($entity);

        $model->setEntity($entityClassname);
        $model->setEntityKey($modelId);

        $logger = $serviceLocator->get('logger');
        $model->setLogger($logger);

        $model->setEntityManager($serviceLocator->get('doctrine.entitymanager.' . $model->getOrm()));

        return $model;
    }
}