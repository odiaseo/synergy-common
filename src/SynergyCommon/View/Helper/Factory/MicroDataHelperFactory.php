<?php
namespace SynergyCommon\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use SynergyCommon\View\Helper\MicroData;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class FlashMessagesHelperFactory
 * @package SynergyCommon\View\Helper\Factory
 */
class MicroDataHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return MicroData
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {

        return new MicroData($serviceLocator, $serviceLocator->get('logger'));
    }
}
