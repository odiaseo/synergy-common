<?php

namespace SynergyCommon\Event\Listener;

use Doctrine\Common\Proxy\Autoloader;
use Doctrine\ORM\EntityManager;
use Gedmo\Loggable\LoggableListener;
use SynergyCommon\PageRendererInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\Expires;
use Laminas\Http\Header\Pragma;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\Http\TranslatorAwareTreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\Container;
use Laminas\Session\SessionManager;
use Laminas\View\Model\JsonModel;

/**
 * Class SynergyModuleListener
 *
 * @package SynergyCommon\Event\Listener
 */
class SynergyModuleListener implements ListenerAggregateInterface
{
    protected static $initialised = false;
    protected static $handled = false;
    protected $listeners = [];
    protected $callback;

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'handleException'], 25);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'handleException'], 25);

        //$this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'initSession'), 50000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onPreRoute'], 200);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'initEntityManager'], -500);
        // $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'compressOutput'], 103);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'setHeaders'], -100);
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
        $callback = function ($event) use ($services) {
            /** @var MvcEvent $event */
            $exception = $event->getResult()->exception;

            if (!$exception) {
                return;
            } elseif ($services->has('logger')) {
                $service = $services->get('logger');
                if ($exception instanceof \Exception) {
                    $service->logException($exception);
                } elseif ($exception instanceof \Error) {
                    $service->error($exception->__toString());
                }
            }
        };

        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, $callback);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, $callback);
    }

    /**
     * Handle errors and logging
     *
     * @param MvcEvent $event
     */
    public function handleException(MvcEvent $event)
    {
        $logException = false;
        if ($event->isError() and static::$handled === false) {
            $services = $event->getApplication()->getServiceManager();

            /** @var $request \Laminas\Http\PhpEnvironment\Request */
            $request = $event->getRequest();
            $callback = $this->getCallback();

            if ($request instanceof Request and $request->isXmlHttpRequest()) {
                $viewModel = new JsonModel();
                $viewModel->setVariables(
                    [
                        'error' => true,
                        'message' => $event->getError(),
                    ]
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

            if ($logException and $services->has('logger')) {
                /** @var $logget \Laminas\Log\Logger */
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

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param MvcEvent $event
     */
    public function initSession(MvcEvent $event)
    {
        /** @var $e \Laminas\Mvc\MvcEvent */
        $app = $event->getApplication();
        $serviceManager = $app->getServiceManager();
        $request = $serviceManager->get('Request');

        if (php_sapi_name() != 'cli' and $request instanceof Request) {
            /** @var $session SessionManager */
            $session = $serviceManager->get(SessionManager::class);
            $session->start();

            if ($serviceManager->has('active\site')) {
                $site = $serviceManager->get('active\site');
                $namespace = $site->getSessionNamespace();
            } else {
                $namespace = 'initialised';
            }

            /** @var $container \Laminas\Session\Container */
            $container = new Container($namespace, $session);

            if (isset($container->init)) {
                return;
            }

            $session->regenerateId(true);
            $container->init = 1;
            $container->remoteAddr = $request->getServer()->get('REMOTE_ADDR');
            $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');

            $config = $serviceManager->get('Config');

            if (empty($config['session'])) {
                return;
            }

            $sessionConfig = $config['session'];

            if (!isset($sessionConfig['validators'])) {
                return;
            }

            $chain = $session->getValidatorChain();

            foreach ($sessionConfig['validators'] as $validator) {
                switch ($validator) {
                    case \Laminas\Session\Validator\HttpUserAgent::class:
                        $validator = new $validator($container->httpUserAgent);
                        break;
                    case \Laminas\Session\Validator\RemoteAddr::class:
                        $validator = new $validator($container->remoteAddr);
                        break;
                    default:
                        $validator = new $validator();
                }

                $chain->attach('session.validate', [$validator, 'isValid']);
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
        /** @var $sm \Laminas\ServiceManager\ServiceManager */
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
            $siteFilter->setSiteList($site->getAllowedSites());

            /** @var \SynergyCommon\Util\ErrorHandler $logger */
            $logger = $sm->get('logger');
            $siteFilter->setLogger($logger);

            foreach ($em->getEventManager()->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($listener instanceof SiteAwareListener and !$listener->hasSite()) {
                        $listener->setSite($site);
                    }

                    if ($listener instanceof LoggableListener) {
                        $listener->setUsername('system');
                    }
                }
            }

            $config = $sm->get('Config');
            foreach ($config['doctrine']['configuration'] as $name => $data) {
                $proxyNamespace = $data['proxy_namespace'];
                $path = ltrim($data['proxy_dir'], DIRECTORY_SEPARATOR);
                $proxyDir = getcwd() . DIRECTORY_SEPARATOR . $path;

                Autoloader::register($proxyDir, $proxyNamespace, [$logger, 'logProxyNotFound']);
            }

            $this->setCliTimeout($em);
            $this->setDbConnectionCleanup($em);

            return;
        }
    }

    private function setCliTimeout(EntityManager $entityManager)
    {
        if (php_sapi_name() == 'cli') {
            $init = ' SET NAMES utf8mb4; SET session wait_timeout=28800; set innodb_lock_wait_timeout=6000;';
            $statement = $entityManager->getConnection()->prepare($init);
            $statement->execute();
        }
    }

    private function setDbConnectionCleanup(EntityManager $entityManager)
    {
        if (PHP_SAPI == 'cli') {
            if (function_exists('pcntl_async_signals')) {
                pcntl_async_signals(true);
            } else {
                declare(ticks = 1);
            }

            $handler = function () use ($entityManager) {
                $entityManager->getConnection()->close();
                \posix_kill(\posix_getpid(), SIGKILL);
            };

            if (function_exists('pcntl_signal')) {
                \pcntl_signal(SIGINT, $handler);
                \pcntl_signal(SIGTERM, $handler);
                \pcntl_signal(SIGTSTP, $handler);
            }
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
            /** @var $serviceManager \Laminas\ServiceManager\ServiceManager */
            $serviceManager = $app->getServiceManager();
            $router = $serviceManager->get('router');
            if ($router and $router instanceof TranslatorAwareTreeRouteStack) {
                if ($serviceManager->has('route\translator')) {
                    /** @var $translator \Laminas\Mvc\I18n\Translator */
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
     * @param MvcEvent $event
     */
    public function compressOutput(MvcEvent $event)
    {
        $config = $event->getApplication()->getServiceManager()->get('Config');
        if ((defined('APPLICATION_ENV') and APPLICATION_ENV == 'production') || $config['synergy']['compress_output']) {
            $response = $event->getResponse();

            if ($response instanceof Response) {
                $content = $response->getBody();
                $content = preg_replace('/(?<=>)\s+|\s+(?=<)/', ' ', $content);
                $response->setContent($content);
            }
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function setHeaders(MvcEvent $event)
    {
        /** @var $authService \Laminas\Authentication\AuthenticationService */
        /** @var $serviceManager \Laminas\ServiceManager\ServiceManager */
        /** @var HttpResponse $response */

        $serviceManager = $event->getApplication()->getServiceManager();
        $response = $event->getResponse();
        $cacheStatus = $serviceManager->get('synergy\cache\status');

        if ($response instanceof HttpResponse) {
            $headers = $response->getHeaders();
            if ($serviceManager->has(AuthenticationService::class)) {
                $authService = $serviceManager->get(AuthenticationService::class);
                $hasIdentity = $authService->hasIdentity();
            } else {
                $hasIdentity = false;
            }

            if (!$hasIdentity and ($response->isSuccess() or $response->isRedirect()) and $cacheStatus->enabled) {
                $config = $serviceManager->get('Config');
                $max = 1 * $config['synergy']['cache_control'];
                $rand = (int)mt_rand(4, 24);
                $hours = abs($max + $rand);
                $age = $hours * 3600;
                $expire = new \DateTime("+{$hours} hours");

                //$nginxExpire = $age * 2;

                $headers->addHeader(CacheControl::fromString("Cache-Control: public, max-age={$age}"))
                    ->addHeader(Expires::fromString("Expires: {$expire->format('r')}"))
                    //->addHeader(GenericHeader::fromString("X-Accel-Expires: {$nginxExpire}"))
                    ->addHeader(Pragma::fromString('Pragma: cache'));


            } else {
                $expire = new \DateTime("-7 days");
                $headers->addHeader(CacheControl::fromString("Cache-Control: max-age=0, no-cache"))
                    ->addHeader(Expires::fromString("Expires: {$expire->format('r')}"))
                    ->addHeader(Pragma::fromString('Pragma: no-cache'));
            }

            $response->setHeaders($headers);
        }
    }
}
