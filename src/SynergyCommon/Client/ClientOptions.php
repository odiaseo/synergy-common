<?php
namespace SynergyCommon\Client;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ClientOptions
 *
 * @package SynergyCommon\Client
 */
class ClientOptions extends AbstractOptions
{
    protected $_domain;

    protected $_headers;

    public function setDomain($domain)
    {
        $this->_domain = $domain;
    }

    public function getDomain()
    {
        return $this->_domain;
    }

    public function setHeaders($headers)
    {
        $this->_headers = $headers;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

}