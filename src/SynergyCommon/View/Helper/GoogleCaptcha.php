<?php

namespace SynergyCommon\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * Class FormPlainText
 * @package SynergyCommon\View\Helper
 */
class GoogleCaptcha extends AbstractHelper
{
    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $attributes          = $element->getAttributes();
        $attributes['class'] = 'g-recaptcha';

        return sprintf(
            '<div %s%s',
            $this->createAttributesString($attributes),
            '></div>'
        );
    }

    public function __invoke(ElementInterface $element = null)
    {
        return $this->render($element);
    }
}
