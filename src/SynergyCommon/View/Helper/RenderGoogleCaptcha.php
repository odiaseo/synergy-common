<?php

namespace SynergyCommon\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * Class FormPlainText
 * @package SynergyCommon\View\Helper
 */
class RenderGoogleCaptcha extends AbstractHelper
{
    /**
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $attributes                 = $element->getAttributes();
        $attributes['class']        = 'g-recaptcha';
        //$attributes['data-size']    = 'compact';
        $attributes['data-sitekey'] = $element->getOption('site_key');

        return sprintf(
            '<div %s></div>',
            $this->createAttributesString($attributes)
        );
    }

    public function __invoke(ElementInterface $element = null)
    {
        return $this->render($element);
    }
}
