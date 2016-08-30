<?php
namespace SynergyCommon\Controller;

use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;

/**
 * Class BaseRestfulController
 * @method sendPayload($payload)
 * @package SynergyCommon\Controller
 */
abstract class BaseActionController extends AbstractActionController
{
    use ServiceLocatorAwareTrait;

    public function __construct(ServiceManager $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }
}
