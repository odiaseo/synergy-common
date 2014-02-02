<?php
namespace SynergyCommon\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as FlashMessenger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class FlashMessages
    extends AbstractHelper
    implements ServiceLocatorAwareInterface
{
    /**
     * @var FlashMessenger
     */
    protected $flashMessenger;

    /** @var \Zend\Mvc\Controller\PluginManager */
    protected $_serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }


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