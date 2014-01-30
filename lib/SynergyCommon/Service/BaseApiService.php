<?php
namespace SynergyCommon\Service;

use Zend\Json\Json;

class BaseApiService
    extends BaseService
{

    /**
     * @var \SynergyCommon\Client\HttpRestJsonClient
     */
    protected $_client;

    /**
     * Process API request
     *
     * @param        $url
     * @param string $method HTTP Method (GET, POST, DELETE, PUT)
     * @param null   $params
     *
     * @return array
     */
    public function processRequest($url, $method = 'GET', $params = null)
    {
        try {
            $method = strtoupper($method);

            return $this->_client->dispatchRequestAndDecodeResponse($url, $method, $params);
        } catch (\Exception $e) {
            $this->_logger->logException($e);

            return array(
                'error'   => true,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * @param \SynergyCommon\Client\HttpRestJsonClient $client
     */
    public function setClient($client)
    {
        $this->_client = $client;
    }

    /**
     * @return \SynergyCommon\Client\HttpRestJsonClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Get site details
     *
     * @param null $domain
     *
     * @return mixed
     */
    public function getSiteDetails($domain = null)
    {
        if (!$domain) {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $this->_serviceManager->get('application')->getRequest();
            $domain  = $this->_filterHostName($request->getServer('HTTP_HOST'));
        }

        $url  = sprintf('/affiliate/apifunction/%s/%s', 'site', 'findOneByDomain');
        $site = $this->getClient()->dispatchRequestAndDecodeResponse($url, 'GET', array('params' => array($domain)));

        return $site;
    }

}