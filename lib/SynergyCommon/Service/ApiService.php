<?php
namespace SynergyCommon\Service;


use SynergyCommon\Service\BaseService;

class ApiService
    extends BaseService
{

    /** @var \SynergyCommon\Util\ErrorHandler */
    protected $_logger;
    /**
     * @var \Zend\Http\Client
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
     * @param \Vaboose\Api\Client\HttpRestJsonClient $client
     */
    public function setClient($client)
    {
        $this->_client = $client;
    }

    /**
     * @return \Vaboose\Api\Client\HttpRestJsonClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @param \SynergyCommon\Util\ErrorHandler $logger
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @return \SynergyCommon\Util\ErrorHandler
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}