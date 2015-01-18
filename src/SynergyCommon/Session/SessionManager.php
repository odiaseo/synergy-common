<?php
namespace SynergyCommon\Session;

use Zend\Cache\StorageFactory as CacheStorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\SessionManager as ZendSessionManager;

/**
 * Class SessionManager
 *
 * @package SynergyCommon\Session
 */
class SessionManager implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        if (isset($config['session'])) {
            $session = $config['session'];

            $sessionConfig = null;
            if (isset($session['config'])) {
                $class   = isset($session['config']['class'])
                    ? $session['config']['class']
                    : 'Zend\Session\Config\SessionConfig';
                $options = isset($session['config']['options']) ? $session['config']['options']
                    : array();
                /** @var $sessionConfig \Zend\Session\Config\SessionConfig */
                $sessionConfig = new $class();
                $sessionConfig->setOptions($options);
            }

            $sessionStorage = null;
            if (isset($session['storage'])) {
                $class          = $session['storage'];
                $sessionStorage = new $class();
            }

            /** @var $sessionSaveHandler \Zend\Session\SaveHandler\SaveHandlerInterface */
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