<?php

namespace SynergyCommon\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\Helper\AbstractHelper;

class MicroData
    extends AbstractHelper
    implements ServiceManagerAwareInterface
{

    private $_enabled = true;

    /** @var \Zend\View\HelperPluginManager */
    protected $_sm;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_sm = $serviceManager;
    }

    /**
     * @return  \SynergyCommon\View\Helper\MicroData $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param $property
     *
     * @return string
     */
    public function property($property)
    {
        $itemProperty = '';
        if ($this->isPropertyValid($property) and $this->_enabled) {
            $itemProperty = "itemprop=\"{$property}\"";
        }

        return $this->pad($itemProperty);
    }

    /**
     * @param $scope
     *
     * @return string
     */
    public function scope($scope)
    {
        $itemProperty = '';
        if ($scope and $this->_enabled) {
            $itemProperty = "itemscope itemtype=\"http://schema.org/{$scope}\"";
        }

        return $this->pad($itemProperty);
    }

    /**
     * @param $scope
     * @param $property
     *
     * @return string
     */
    public function scopeAndProperty($scope, $property)
    {
        return $this->pad(($this->property($property) . ' ' . $this->scope($scope)));
    }

    protected function pad($text)
    {
        if ($this->_enabled) {
            return ' ' . trim($text) . ' ';
        } else {
            return '';
        }
    }

    protected function isPropertyValid($property)
    {
        $valid = array(
            "additionalType",
            "brand",
            "breadcrumb",
            "category",
            "color",
            "deliveryLeadTime",
            "depth",
            "description",
            "image",
            "itemCondition",
            'ItemList',
            'itemList',
            'itemListElement',
            "logo",
            "manufacturer",
            "model",
            "name",
            'offers',
            "offerUrl",
            "price",
            "priceCurrency",
            'Product',
            "productID",
            "publicationDate",
            "publisher",
            "releaseDate",
            "seller",
            'SiteNavigationElement',
            "sku",
            'SomeProducts',
            "url",
            "validFrom",
            "validThrough",
            'WebPageElement',
            "weight",
            "width",
            'WPFooter',
            'WPHeader'
        );

        if (!in_array($property, $valid)
            and $this->_sm->getServiceLocator()->has('logger')
        ) {
            /** @var $logger \SynergyCommon\Util\ErrorHandler */
            $logger = $this->_sm->getServiceLocator()->get('logger');
            $logger->warn('Invalid microData property found: ' . $property);
        }

        return true;
    }

    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled;

        return $this;
    }

    public function getEnabled()
    {
        return $this->_enabled;
    }
}

