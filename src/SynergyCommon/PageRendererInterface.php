<?php
namespace SynergyCommon;

use Laminas\Mvc\MvcEvent;

interface PageRendererInterface
{
    public function render(MvcEvent $event);
}