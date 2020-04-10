<?php
namespace SynergyCommon\Controller;

use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Laminas\Mvc\Console\Controller\AbstractConsoleController;
use Laminas\ServiceManager\ServiceManager;

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
