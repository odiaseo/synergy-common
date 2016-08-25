<?php
namespace SynergyCommon\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use SynergyCommon\View\Helper\FlashMessages;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FlashMessagesHelperFactory
 * @package SynergyCommon\View\Helper\Factory
 */
class FlashMessagesHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return FlashMessages
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {

        /** @var FlashMessenger $flashMessenger */
        $pluginManager  = $serviceLocator->get('ControllerPluginManager');
        $flashMessenger = $pluginManager->get('FlashMessenger');
        return new FlashMessages($flashMessenger);
    }
}
