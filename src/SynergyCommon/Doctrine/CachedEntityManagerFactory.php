<?php
namespace SynergyCommon\Doctrine;
;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ActiveClientSiteFactory
 * @package SynergyCommon\Service
 */
class CachedEntityManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $cacheStatus = $serviceLocator->get('synergy\cache\status');

        if ($serviceLocator->has(AuthenticationService::class)) {
            /** @var AuthenticationService $authService */
            $authService = $serviceLocator->get(AuthenticationService::class);
            $identity    = $authService->hasIdentity();
        } else {
            $identity = false;
        }

        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $enabled       = (!$identity and $cacheStatus->enabled);

        return new CachedEntityManager($entityManager, $enabled);
    }
}
