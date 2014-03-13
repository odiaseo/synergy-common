<?php
namespace SynergyCommon\Service;

use Zend\Json\Json;

class BaseApiService
    extends BaseService
    implements ClientAwareInterface
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

            return $this->getClient()->dispatchRequestAndDecodeResponse($url, $method, $params);
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

        return $this->executeRemoteFunction('findOneByDomain', 'GET', array($domain), 'site');
    }

    /**
     * Execute a remote function
     *
     * @param       $fundtionName
     * @param       $method
     * @param array $functionFaramters
     * @param null  $entity
     * @param array $options
     *
     * @return mixed
     */
    public function executeRemoteFunction(
        $fundtionName, $method, $functionFaramters = array(), $entity = null, $options = array()
    ) {
        $url = sprintf('/affiliate/apifunction/%s/%s', $entity, $fundtionName);
        if ($options) {
            $url = $url . '?' . \http_build_query($options);
        }

        return $this->getClient()->dispatchRequestAndDecodeResponse(
            $url, $method, array('params' => $functionFaramters)
        );
    }

}