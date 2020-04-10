<?php

namespace SynergyCommon\View\Helper;

use Interop\Container\ContainerInterface;
use SynergyCommon\Service\ServiceLocatorAwareInterface;
use SynergyCommon\Service\ServiceLocatorAwareTrait;
use SynergyCommon\Util\ErrorHandler;
use Laminas\Log\LoggerInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class MicroData
 * @package SynergyCommon\View\Helper
 */
class MicroData extends AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var ErrorHandler | LoggerInterface
     */
    private $logger;

    /**
     * MicroData constructor.
     * @param ContainerInterface $serviceManager
     * @param ErrorHandler $logger
     */
    public function __construct(ContainerInterface $serviceManager, ErrorHandler $logger)
    {
        $this->setServiceLocator($serviceManager);
        $this->logger = $logger;
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
        if ($this->isPropertyValid($property) and $this->enabled) {
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
        if ($scope and $this->enabled) {
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
        if ($this->enabled) {
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

        if (!in_array($property, $valid)) {
            $this->logger->warn('Invalid microData property found: ' . $property);
        }

        return true;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }
}
