<?php

namespace SynergyCommon\Controller;

use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\ServiceManager;

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

    protected function fromRequest($key, $default = '')
    {
        return $this->params()->fromPost($key, $this->params()->fromQuery($key, $default));
    }
}
