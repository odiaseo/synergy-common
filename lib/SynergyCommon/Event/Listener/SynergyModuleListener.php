<?php
namespace SynergyCommon\Event\Listener;

use SynergyCommon\Event\Listener\SiteAwareListener;
use SynergyCommon\PageRendererInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class SynergyModuleListener
    implements ListenerAggregateInterface
{
    protected $listeners = array();

    protected $initialised = false;

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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onPreRoute'), 100);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'compressOutput'), 103);

    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function bootstrap(EventManagerInterface $eventManager, ServiceManager $services)
    {
        $eventManager->attach(
            array(
                 MvcEvent::EVENT_RENDER_ERROR,
                 MvcEvent::EVENT_DISPATCH_ERROR
            ),
            function ($event) use ($services) {
                /** @var MvcEvent $event */
                $exception = $event->getResult()->exception;

                if (!$exception) {
                    return;
                } elseif ($services->has('logger')) {
                    $service = $services->get('logger');
                    $service->logException($exception);
                }
            }
        );
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
                /** @var $logger \Zend\Log\Logger */
                $logger = $services->get('logger');
                $logger->err($event->getError() . ': ' . $event->getRequest());
            }

            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $event->getRequest();

            if ($request instanceof Request and $request->isXmlHttpRequest()) {
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

            if ($services->has('error_page')) {
                $errorPageRender = $services->get('error_page');

                if ($errorPageRender instanceof PageRendererInterface) {
                    $errorPageRender->render($event);
                }
            }
        } else {
            return;
        }
    }

    /**
     * Initialise session
     */
    public function initSession(MvcEvent $event)
    {
        /** @var $e \Zend\Mvc\MvcEvent */
        $app = $event->getApplication();
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
        if (!$this->initialised) {
            /** @var $sm \Zend\ServiceManager\ServiceManager */
            $sm = $e->getApplication()->getServiceManager();

            if ($sm->has('active_site')) {
                /** @var $site \SynergyCommon\Entity\BaseSite */
                $site = $sm->get('active_site');

                $viewModel = $e->getViewModel();
                $viewModel->setVariable('site', $site);

                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $sm->get('doctrine.entitymanager.orm_default');

                //enable filters
                /** @var $siteFilter \SynergyCommon\Doctrine\Filter\SiteFilter */
                $siteFilter = $em->getFilters()->enable("site-specific");
                $siteFilter->setSite($site);

                if ($sm->has('logger')) {
                    $siteFilter->setLogger($sm->get('logger'));
                }

                foreach ($em->getEventManager()->getListeners() as $listeners) {
                    foreach ($listeners as $listener) {
                        if ($listener instanceof SiteAwareListener and !$listener->hasSite()) {
                            $listener->setSite($site);
                        }
                    }
                }
            }

            $this->initialised = true;
        }
    }

    /**
     * Add Route translator
     *
     * @param MvcEvent $e
     */
    public function onPreRoute(MvcEvent $e)
    {
        if ($e->getApplication()->getRequest() instanceof Request) {
            $app = $e->getTarget();

            /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
            $serviceManager = $app->getServiceManager();

            if ($serviceManager->has('translator')) {
                $translator = $serviceManager->get('translator');
                if ($router = $serviceManager->get('router') and method_exists($router, 'setTranslator')) {
                    $router->setTranslator($translator);
                }
            }
        }

    }

    /**
     * Compress HTML output
     *
     * @param MvcEvent $e
     */
    public function compressOutput(MvcEvent $e)
    {
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'production') {
            $response = $e->getResponse();

            if ($response instanceof Response) {
                $content = $response->getBody();
                $content = preg_replace('/(?<=>)\s+|\s+(?=<)/', ' ', $content);
                $response->setContent($content);
            }
        }
    }
}