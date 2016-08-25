<?php
namespace SynergyCommon\Controller;

use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Zend\Mvc\Console\Controller\AbstractConsoleController;
use Zend\ServiceManager\ServiceManager;

/**
 * Class BaseConsoleController
 * @package SynergyCommon\Controller
 */
abstract class BaseConsoleController extends AbstractConsoleController
{
    use ServiceLocatorAwareTrait;

    public function __construct(ServiceManager $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }
}
