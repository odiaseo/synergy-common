<?php
namespace SynergyCommon\Event\Listener;

use Doctrine\Common\Proxy\Autoloader;
use SynergyCommon\PageRendererInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class SynergyModuleListener
    implements ListenerAggregateInterface
{
    protected $listeners = array();

    protected static $initialised = false;

    protected $callback;
    protected static $handled = false;

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

        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'initSession'), 20000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initEntityManager'), 103);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onPreRoute'), 100);
        //$this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'compressOutput'), 103);

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
        $logException = false;
        if ($event->isError() && static::$handled === false) {
            $services = $event->getApplication()->getServiceManager();

            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $event->getRequest();
            $callback = $this->getCallback();

            if ($request instanceof Request && $request->isXmlHttpRequest()) {
                $viewModel = new JsonModel();
                $viewModel->setVariables(
                    array(
                        'error'   => true,
                        'message' => $event->getError()
                    )
                );
                $viewModel->setTerminal(true);
                $event->setResult($viewModel);
                $logException = true;
            }

            if ($services->has('error_page')) {
                $errorPageRender = $services->get('error_page');

                if ($errorPageRender instanceof PageRendererInterface) {
                    $errorPageRender->render($event);
                }
            } elseif (is_callable($callback)) {
                call_user_func($callback, $request);
            } else {
                $logException = true;
            }

            if ($logException && $services->has('logger')) {
                /** @var $logget \Zend\Log\Logger */
                $logger = $services->get('logger');
                $uri = '';
                if ($request instanceof Request) {
                    $uri = $request->getUriString();
                }
                $logger->err($event->getError() . ': ' . $uri);
            }
            static::$handled = true;
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function initSession(MvcEvent $event)
    {
        /** @var $e \Zend\Mvc\MvcEvent */
        $app = $event->getApplication();
        $sm = $app->getServiceManager();

        if ($app->getRequest() instanceof Request) {
            if ($sm->has('session_manager')) {
                /** @var $session \Zend\Session\SessionManager */
                $session = $sm->get('session_manager');
                $session->start();

                if ($sm->has('active\site')) {
                    $site = $sm->get('active\site');
                    $namespace = $site->getSessionNamespace();
                } else {
                    $namespace = 'initialised';
                }

                /** @var $container \Zend\Session\Container */
                $container = new Container($namespace, $session);

                if (!isset($container->init) && php_sapi_name() != 'cli') {
                    $session->regenerateId(true);
                    $container->init = 1;
                }
            }
        }
    }

    /**
     * Set filters on siteaware modules
     *
     * @param MvcEvent $event
     *
     * @return null
     */
    public function initEntityManager(MvcEvent $event)
    {
        if (!static::$initialised) {
            /** @var $sm \Zend\ServiceManager\ServiceManager */
            $sm = $event->getApplication()->getServiceManager();

            if ($sm->has('active\site')) {
                /** @var $site \SynergyCommon\Entity\BaseSite */
                $site = $sm->get('active\site');

                $viewModel = $event->getViewModel();
                $viewModel->setVariable('site', $site);

                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $sm->get('doctrine.entitymanager.orm_default');

                //enable filters
                /** @var $siteFilter \SynergyCommon\Doctrine\Filter\SiteFilter */
                $em->getFilters()->enable("soft-delete");
                $siteFilter = $em->getFilters()->enable("site-specific");

                $siteFilter->setSite($site);
                $siteFilter->setServiceLocator($sm);
                /** @var \SynergyCommon\Util\ErrorHandler $logger */
                $logger = $sm->get('logger');
                $siteFilter->setLogger($logger);

                foreach ($em->getEventManager()->getListeners() as $listeners) {
                    foreach ($listeners as $listener) {
                        if ($listener instanceof SiteAwareListener and !$listener->hasSite()) {
                            $listener->setSite($site);
                        }
                    }
                }

                $config = $sm->get('config');
                foreach ($config['doctrine']['configuration'] as $name => $data) {
                    $proxyNamespace = $data['proxy_namespace'];
                    $path = ltrim($data['proxy_dir'], DIRECTORY_SEPARATOR);
                    $proxyDir = getcwd() . DIRECTORY_SEPARATOR . $path;

                    Autoloader::register($proxyDir, $proxyNamespace, array($logger, 'logProxyNotFound'));
                }
            }

            static::$initialised = true;

            return null;
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
            $router = $serviceManager->get('router');
            if ($router && $router instanceof TranslatorAwareTreeRouteStack) {
                if ($serviceManager->has('route\translator')) {
                    /** @var $translator \Zend\Mvc\I18n\Translator */
                    $translator = $serviceManager->get('route\translator');
                } elseif ($serviceManager->has('translator')) {
                    $translator = $serviceManager->get('translator');
                } else {
                    $translator = null;
                }

                if ($translator) {
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

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    public function getCallback()
    {
        return $this->callback;
    }

}
