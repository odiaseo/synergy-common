<?php
namespace SynergyCommon\View\Helper;

use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Helper\AbstractHelper;

/**
 * Class FlashMessages
 * @package SynergyCommon\View\Helper
 */
class FlashMessages extends AbstractHelper
{
    /**
     * @var FlashMessenger
     */
    private $flashMessenger;

    /**
     * FlashMessages constructor.
     * @param FlashMessenger $flashMessenger
     */
    public function __construct(FlashMessenger $flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
    }

    public function getFlashMessenger()
    {
        return $this->flashMessenger;
    }

    public function __invoke($includeMessages = false)
    {
        $messages = array(
            FlashMessenger::NAMESPACE_ERROR   => array(),
            FlashMessenger::NAMESPACE_SUCCESS => array(),
            FlashMessenger::NAMESPACE_INFO    => array(),
            FlashMessenger::NAMESPACE_DEFAULT => array()
        );

        foreach ($messages as $ns => &$m) {
            $m = $this->getFlashMessenger()->getMessagesFromNamespace($ns);
            if ($includeMessages) {
                $m = array_merge($m, $this->getFlashMessenger()->getCurrentMessagesFromNamespace($ns));
                $this->getFlashMessenger()->clearCurrentMessagesFromNamespace($ns);
            }
        }

        return $messages;
    }
}
