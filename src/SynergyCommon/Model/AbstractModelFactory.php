<?php
namespace SynergyCommon\Model;

use Interop\Container\ContainerInterface;
use SynergyCommon\Doctrine\CachedEntityManager;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class AbstractModelFactory
 * @package SynergyCommon\Model
 */
class AbstractModelFactory implements AbstractFactoryInterface
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
     * @return AbstractModel
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $modelId   = str_replace($this->_configPrefix, '', $requestedName);
        $modelName = __NAMESPACE__ . '\\' . ucfirst($modelId) . 'Model';

        /** @var $model \SynergyCommon\Model\AbstractModel */
        $model = new $modelName();

        $entity          = $serviceLocator->get('synergycommon\entity\\' . $modelId);
        $entityClassname = get_class($entity);

        $model->setEntity($entityClassname);
        $model->setEntityKey($modelId);

        $logger = $serviceLocator->get('logger');
        $model->setLogger($logger);
        $model->setServiceLocator($serviceLocator);

        $entityManager = $serviceLocator->get('doctrine.entitymanager.' . $model->getOrm());
        $cachedManager = new CachedEntityManager($entityManager, false);

        $model->setEntityManager($cachedManager);

        return $model;
    }
}
