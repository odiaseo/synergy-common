<?php
namespace SynergyCommon\Service;
use Laminas\Http\Request;

/**
 * Class BaseApiService
 * @package SynergyCommon\Service
 */
class BaseApiService extends BaseService implements ClientAwareInterface
{

    /**
     * @var \SynergyCommon\Client\HttpRestJsonClient
     */
    protected $client;

    /**
     * Process API request
     *
     * @param        $url
     * @param string $method HTTP Method (GET, POST, DELETE, PUT)
     * @param null $params
     *
     * @return array
     */
    public function processRequest($url, $method = 'GET', $params = null)
    {
        try {
            $method = strtoupper($method);

            return $this->getClient()->dispatchRequestAndDecodeResponse($url, $method, $params);
        } catch (\Exception $e) {
            $this->getLogger()->logException($e);

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
        $this->client = $client;
    }

    /**
     * @return \SynergyCommon\Client\HttpRestJsonClient
     */
    public function getClient()
    {
        return $this->client;
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
        $request = $this->getServiceLocator()->get('application')->getRequest();
        if (!$domain and $request instanceof Request) {
            /** @var $request \Laminas\Http\PhpEnvironment\Request */
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
     * @param null $entity
     * @param array $options
     *
     * @return mixed
     */
    public function executeRemoteFunction(
        $fundtionName, $method, $functionFaramters = array(), $entity = null, $options = array()
    )
    {
        $url = sprintf('/affiliate/apifunction/%s/%s', $entity, $fundtionName);
        if ($options) {
            $url = $url . '?' . \http_build_query($options);
        }

        return $this->getClient()->dispatchRequestAndDecodeResponse(
            $url, $method, array('params' => $functionFaramters)
        );
    }
}
