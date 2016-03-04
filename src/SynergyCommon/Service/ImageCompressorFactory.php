<?php
namespace SynergyCommon\Service;

use SynergyCommon\Image\Config\ImageCompressorOptions;
use SynergyCommon\Image\ImageCompressor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ImageCompressorFactory
 * @package SynergyCommon\Service
 */
class ImageCompressorFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceLocator \Zend\ServiceManager\ServiceManager */
        $config      = $serviceLocator->get('config');
        $imageConfig = isset($config['synergy']['image_compressor']) ? $config['synergy']['image_compressor']
            : array();

        $compressorConfig = new ImageCompressorOptions($imageConfig);

        if ($compressorConfig->getAdapter()) {
            /** @var $dapter \SynergyCommon\Image\TransferAdapterInterface */
            $adapter = $serviceLocator->get($compressorConfig->getAdapter());
            $adapter->setOptions($compressorConfig);
            $compressorConfig->setAdapter($adapter);
        }

        $service = new ImageCompressor();
        $service->setConfig($compressorConfig);
        $service->setServiceManager($serviceLocator);

        if ($serviceLocator->has('logger')) {
            $logger = $serviceLocator->get('logger');
            $service->setLogger($logger);
        }

        return $service;
    }
}
