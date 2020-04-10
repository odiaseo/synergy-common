<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use SynergyCommon\Image\Config\ImageCompressorOptions;
use SynergyCommon\Image\ImageCompressor;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ImageCompressorFactory
 * @package SynergyCommon\Service
 */
class ImageCompressorFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var $serviceLocator \Laminas\ServiceManager\ServiceManager */
        $config      = $serviceLocator->get('Config');
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
        $service->setServiceLocator($serviceLocator);

        if ($serviceLocator->has('logger')) {
            $logger = $serviceLocator->get('logger');
            $service->setLogger($logger);
        }

        return $service;
    }
}
