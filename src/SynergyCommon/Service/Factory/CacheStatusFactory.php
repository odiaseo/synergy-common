<?php
namespace SynergyCommon\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Console\Request;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class CacheStatusFactory
 * @package SynergyCommon\Service\Factory
 */
class CacheStatusFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return object
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var $authService \Laminas\Authentication\AuthenticationService */
        /** @var ServiceLocatorInterface $serviceLocator */
        $request = $serviceLocator->get('request');
        $config  = $serviceLocator->get('Config');

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
