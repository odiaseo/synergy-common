<?php
namespace SynergyCommon\Session;

use Interop\Container\ContainerInterface;
use Laminas\Cache\StorageFactory as CacheStorageFactory;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use Laminas\Session\SaveHandler\Cache;
use Laminas\Session\SessionManager as ZendSessionManager;

/**
 * Class SessionManager
 *
 * @package SynergyCommon\Session
 */
class SessionManager implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return ZendSessionManager
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config = $serviceLocator->get('Config');
        if (isset($config['session'])) {
            $session = $config['session'];

            $sessionConfig = null;
            if (isset($session['config'])) {
                $class   = isset($session['config']['class'])
                    ? $session['config']['class']
                    : 'Laminas\Session\Config\SessionConfig';
                $options = isset($session['config']['options']) ? $session['config']['options']
                    : array();
                /** @var $sessionConfig \Laminas\Session\Config\SessionConfig */
                $sessionConfig = new $class();
                $sessionConfig->setOptions($options);
            }

            $sessionStorage = null;
            if (isset($session['storage'])) {
                $class          = $session['storage'];
                $sessionStorage = new $class();
            }

            /** @var $sessionSaveHandler \Laminas\Session\SaveHandler\SaveHandlerInterface */
            $sessionSaveHandler = null;
            if (isset($session['save_handler']['cache'])) {
                $cache              = CacheStorageFactory::factory($session['save_handler']['cache']);
                $sessionSaveHandler = new Cache($cache);
            }

            $sessionManager = new ZendSessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

            if (isset($session['validators'])) {
                $chain = $sessionManager->getValidatorChain();
                foreach ($session['validators'] as $validator) {
                    $validator = new $validator();
                    $chain->attach('session.validate', array($validator, 'isValid'));
                }
            }
        } else {
            $sessionManager = new ZendSessionManager();
        }

        Container::setDefaultManager($sessionManager);

        return $sessionManager;
    }
}
