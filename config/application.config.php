<?php

if (!defined('APPLICATION_ENV')) {
    if (isset($_SERVER['APPLICATION_ENV'])) {
        $env = $_SERVER['APPLICATION_ENV'];
    } elseif (!$env = getenv('APPLICATION_ENV')) {
        $env = 'production';
    }

    define('APPLICATION_ENV', $env);
}

\ini_set('default_charset', 'utf-8');

date_default_timezone_set('UTC');

if (APPLICATION_ENV == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
} else {
    ini_set('display_errors', 'off');
}

return array(
    // This should be an array of module namespaces used in the application.
    'modules' => array(
        'Zend\Db',
        'Zend\Log',
        'Zend\Mail',
        'Zend\Mvc\Console',
        'Zend\Mvc\I18n',
        'Zend\I18n',
        'Zend\Mvc\Plugin\FilePrg',
        'Zend\Mvc\Plugin\Identity',
        'Zend\Mvc\Plugin\Prg',
        'Zend\Navigation',
        'Zend\Serializer',
        'Zend\ServiceManager\Di',
        'Zend\Session',
        'Zend\Cache',
        'Zend\Form',
        'Zend\InputFilter',
        'Zend\Filter',
        'Zend\Paginator',
        'Zend\Hydrator',
        'Zend\Validator',
        'Zend\Router',
        'Zend\Mvc\Plugin\FlashMessenger',
        'DoctrineModule',
        'DoctrineORMModule',
        'SynergyCommon',
    ),

    'module_listener_options' => array(
        'module_paths'             => array(
            './module',
            './vendor',
        ),
        'config_glob_paths'        => array(
            'config/autoload/{,*.}{global,' . APPLICATION_ENV . ',local}.php',
        ),
        'config_cache_enabled'     => false,
        'module_map_cache_enabled' => false,
        'cache_dir'                => 'data/cache',
    ),
);