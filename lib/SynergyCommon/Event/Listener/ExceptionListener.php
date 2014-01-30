<?php
namespace SynergyCommon\Event\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class ExceptionListener
    implements ListenerAggregateInterface
{
    protected $listeners = array();

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            array(
                 MvcEvent::EVENT_RENDER_ERROR,
                 MvcEvent::EVENT_DISPATCH_ERROR
            ),
            array($this, 'handleException')
        );
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function handleException(MvcEvent $event)
    {
        /** @var $exception \Exception */
        $exception = $event->getResult()->exception;

        if (!$exception) {
            return;
        } else {
            $services = $event->getApplication()->getServiceManager();
            if ($services->has('logger')) {
                $logger = $services->get('logger');
                $logger->logException($exception);
            }

            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $event->getRequest();

            if ($request->isXmlHttpRequest()) {
                $viewModel = new JsonModel();
                $viewModel->setVariables(
                    array(
                         'error'   => true,
                         'message' => $exception->getMessage()
                    )
                );
                $viewModel->setTerminal(true);
                $event->setResult($viewModel);
            }
        }
    }
}