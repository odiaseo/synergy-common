<?php
namespace SynergyCommon;

use Zend\Mvc\MvcEvent;

interface PageRendererInterface
{
    public function render(MvcEvent $event);
}