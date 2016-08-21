<?php
namespace SynergyCommon\View\Helper;

use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Zend\Mvc\Controller\Plugin\FlashMessenger as FlashMessenger;
use Zend\View\Helper\AbstractHelper;

/**
 * Class FlashMessages
 * @package SynergyCommon\View\Helper
 */
class FlashMessages extends AbstractHelper
{
    use ServiceLocatorAwareTrait;
    /**
     * @var FlashMessenger
     */
    protected $flashMessenger;

    public function getFlashMessenger()
    {
        if (!$this->flashMessenger) {
            /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
            $serviceManager = $this->getServiceLocator()->getServiceLocator();

            /** @var $pluginManager \Zend\Mvc\Controller\PluginManager */
            $pluginManager        = $serviceManager->get('ControllerPluginManager');
            $this->flashMessenger = $pluginManager->get('flashmessenger');
        }

        return $this->flashMessenger;
    }

    public function __invoke($includeCurrentMessages = false)
    {
        $messages = array(
            FlashMessenger::NAMESPACE_ERROR   => array(),
            FlashMessenger::NAMESPACE_SUCCESS => array(),
            FlashMessenger::NAMESPACE_INFO    => array(),
            FlashMessenger::NAMESPACE_DEFAULT => array()
        );

        foreach ($messages as $ns => &$m) {
            $m = $this->getFlashMessenger()->getMessagesFromNamespace($ns);
            if ($includeCurrentMessages) {
                $m = array_merge($m, $this->getFlashMessenger()->getCurrentMessagesFromNamespace($ns));
                $this->getFlashMessenger()->clearCurrentMessagesFromNamespace($ns);
            }
        }

        return $messages;
    }
}
