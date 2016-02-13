<?php
namespace SynergyCommon\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\ValidatorInterface;

/**
 * Class GoogleRecaptcha
 * @package SynergyComm\Form\Element
 */
class GoogleCaptcha extends Element implements InputProviderInterface
{
    const RESPONSE_KEY = 'g-recaptcha-response';

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'name'       => $this->getName(),
            'required'   => true,
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                [
                    'name'    => 'SynergyCommon\Validator\GoogleCaptchaValidator',
                    'options' => $this->getOptions()
                ]
            ),
        ];
    }
}
