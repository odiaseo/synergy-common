<?php
namespace SynergyCommon\Validator;

use Zend\Captcha\ReCaptcha;
use Zend\Validator\AbstractValidator;

/**
 * Class GoogleRecaptchaValidator
 * @package SynergyCommon\Validator
 */
class GoogleCaptchaValidator extends AbstractValidator
{
    const END_POINT = 'https://www.google.com/recaptcha/api/siteverify';

    public function isValid($value)
    {
        try {
            $postData   = [
                'secret'   => $this->getOption('secretKey'),
                'response' => $value,
                'remoteip' => $this->getOption('remoteIp'),
            ];
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, self::END_POINT);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postData);
            $curlResponse = curl_exec($curlHandle);

            $data = json_decode($curlResponse, true);
            if (isset($data['success']) and $data['success']) {
                return true;
            }
        } catch (\Exception $exception) {
        }

        $this->error(ReCaptcha::ERR_CAPTCHA);

        return false;
    }
}
