<?php
namespace SynergyCommon\Service;

use SynergyCommon\Image\Config\ImageCompressorOptions;
use SynergyCommon\Image\ImageCompressor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class ImageCompressorFactory
    implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceLocator \Zend\ServiceManager\ServiceManager */
        $config      = $serviceLocator->get('config');
        $imageConfig = isset($config['synergy']['image_compressor']) ? $config['synergy']['image_compressor']
            : array();

        $config = new ImageCompressorOptions($imageConfig);

        if ($config->getAdapter()) {
            /** @var $dapter \SynergyCommon\Image\TransferAdapterInterface */
            $adapter = $serviceLocator->get($config->getAdapter());
            $adapter->setOptions($config);
        }

        $service = new ImageCompressor();
        $service->setConfig($config);
        $service->setServiceManager($serviceLocator);

        if ($serviceLocator->has('logger')) {
            $logger = $serviceLocator->get('logger');
            $service->setLogger($logger);
        }

        return $service;

    }
}