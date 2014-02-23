<?php
namespace SynergyCommon\Service;

use SynergyCommon\Image\Config\ImageProcessorOptions;
use SynergyCommon\Image\ImageProcessor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class ImageProcessorFactory
    implements FactoryInterface
{
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;


    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceLocator \Zend\ServiceManager\ServiceManager */
        $config      = $this->_serviceManager->get('config');
        $imageConfig = isset($config['synergy']['image_compression']) ? $config['synergy']['image_compression']
            : array();

        if (!empty($imageConfig['adapter'])) {
            $imageConfig['adapter'];
            /** @var $dapter \SynergyCommon\Image\TransferAdapterInterface */
            $adapter = $serviceLocator->get($imageConfig['adapter']);
            $adapter->setOptions($config);
            $imageConfig['adapter'] = $adapter;
        }

        $config  = new ImageProcessorOptions($imageConfig);
        $service = new ImageProcessor();
        $service->setConfig($config);
        $service->setServiceManager($serviceLocator);
        if ($this->_serviceManager->has('logger')) {
            $logger = $this->_serviceManager->get('logger');
            $service->setLogger($logger);
        }

        return $service;

    }
}