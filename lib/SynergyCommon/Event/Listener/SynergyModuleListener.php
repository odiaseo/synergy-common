<?php
namespace SynergyCommon\Event\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class SynergyModuleListener
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
            array($this, 'handleException'),
            25
        );

        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'initSession'), 200);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initEntityManager'), 103);

    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Handle errors and logging
     *
     * @param MvcEvent $event
     */
    public function handleException(MvcEvent $event)
    {
        if ($event->isError()) {
            $services = $event->getApplication()->getServiceManager();
            if ($services->has('logger')) {
                /** @var $logger \SynergyCommon\Util\ErrorHandler */
                $logger = $services->get('logger');
                $logger->err($event->getError() . ': ' . $event->getRequest());
            }

            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $event->getRequest();

            if ($request->isXmlHttpRequest()) {
                $viewModel = new JsonModel();
                $viewModel->setVariables(
                    array(
                         'error'   => true,
                         'message' => $event->getError()
                    )
                );
                $viewModel->setTerminal(true);
                $event->setResult($viewModel);
            }
        } else {
            return;
        }
    }

    /**
     * Initialise session
     */
    public function initSession()
    {
        /** @var $e \Zend\Mvc\MvcEvent */
        $app = $e->getApplication();
        $sm  = $app->getServiceManager();

        if ($app->getRequest() instanceof Request) {
            if ($sm->has('session_manager')) {
                $session = $sm->get('session_manager');
                $session->start();

                /** @var $container \Zend\Session\Container */
                $container = new Container();
                if (!isset($container->init) and php_sapi_name() != 'cli') {
                    $session->regenerateId(true);
                    $container->init = 1;
                }
            }
        }
    }

    /**
     * Set filters on siteaware modules
     *
     * @param MvcEvent $e
     */
    public function initEntityManager(MvcEvent $e)
    {
        /** @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        if ($sm->has('active_site')) {
            $site = $sm->get('active_site');

            $viewModel = $e->getViewModel();
            $viewModel->setVariable('site', $site);

            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $sm->get('doctrine.entitymanager.orm_default');

            //enable filters
            /** @var $siteFilter \SynergyCommon\Doctrine\Filter\SiteFilter */
            $siteFilter = $em->getFilters()->enable("site-specific");
            $siteFilter->setServiceManager($sm);
        }
    }
}