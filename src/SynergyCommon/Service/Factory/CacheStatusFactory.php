<?php
namespace SynergyCommon\Service\Factory;

use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CacheStatusFactory
 * @package SynergyCommon\Service\Factory
 */
class CacheStatusFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $authService \Zend\Authentication\AuthenticationService */
        /** @var ServiceLocatorInterface $serviceLocator */
        $request = $serviceLocator->get('request');
        $config  = $serviceLocator->get('config');

        if ($request instanceof Request or php_sapi_name() == 'cli') {
            $enabled = false;
        } elseif (array_key_exists('enable_result_cache', $config)) {
            $enabled = $config['enable_result_cache'];
        } else {
            $enabled = false;
        }

        $return = array('enabled' => $enabled);

        return (object)$return;
    }
}
