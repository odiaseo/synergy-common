<?php
namespace SynergyCommon\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Class GoogleRecaptchaValidator
 * @package SynergyCommon\Validator
 */
class GoogleCaptchaValidator extends AbstractValidator
{

    private $siteKey;
    private $secretKey;
    private $remoteIp;

    public function isValid($value)
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getRemoteIp()
    {
        return $this->remoteIp;
    }

    /**
     * @param mixed $remoteIp
     */
    public function setRemoteIp($remoteIp)
    {
        $this->remoteIp = $remoteIp;
    }

    /**
     * @return mixed
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * @param mixed $siteKey
     */
    public function setSiteKey($siteKey)
    {
        $this->siteKey = $siteKey;
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param mixed $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }
}
