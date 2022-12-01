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
        'Laminas\Di',
        'Laminas\Mvc\I18n',
        'Laminas\I18n',
        'Laminas\Mvc\Plugin\Identity',
        'Laminas\Mvc\Plugin\FilePrg',
        'Laminas\Form',
        'Laminas\InputFilter',
        'Laminas\Filter',
        'Laminas\Mvc\Plugin\FlashMessenger',
        'Laminas\Mvc\Plugin\Prg',
        'Laminas\Session',
        'Laminas\Db',
        'Laminas\Log',
        'Laminas\Mail',
        'Laminas\Navigation',
        'Laminas\Serializer',
        'Laminas\Cache',
        'Laminas\Paginator',
        'Laminas\Hydrator',
        'Laminas\Validator',
        'Laminas\Router',
        'DoctrineModule',
        'DoctrineORMModule',
        'SynergyCommon',
        'Laminas\ZendFrameworkBridge',
        'Laminas\Diactoros',
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
