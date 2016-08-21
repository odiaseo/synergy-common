<?php
namespace SynergyCommon\Model;

use Interop\Container\ContainerInterface;
use SynergyCommon\Doctrine\CachedEntityManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SessionModelFactory
 *
 * @package SynergyCommon\Model
 */
class SessionModelFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $cacheStatus   = $serviceLocator->get('synergy\cache\status');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $cachedManager = new CachedEntityManager($entityManager);
        $model         = new SessionModel();
        $model->setEntityManager($cachedManager);
        $model->setEntity('SynergyCommon\Member\Entity\Session');
        $model->setLogger($serviceLocator->get('logger'));
        $model->setEnableResultCache($cacheStatus->enabled);

        return $model;
    }
}
