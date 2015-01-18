<?php

namespace SynergyCommon\Client;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class HttpRestJsonClient
{
    protected $httpClient;

    /** @var \SynergyCommon\Client\ClientOptions */
    protected $_options;
    /** @var \Zend\Http\Request */
    protected $_request;

    public function __construct(HttpClient $httpClient, $request = null)
    {
        $this->httpClient = $httpClient;
        $this->_request   = $request ?: new Request();
    }

    /**
     * Dispatch request and decode json response
     *
     * @param      $url
     * @param      $method
     * @param null $data
     *
     * @return mixed
     */
    public function dispatchRequestAndDecodeResponse($url, $method, $data = null)
    {
        $request = clone $this->_request;
        $method  = strtoupper($method);
        $request->getHeaders()->addHeaders($this->_options->getHeaders());
        if (strpos($url, 'http://', 0) === false) {
            $endpoint = rtrim($this->_options->getDomain(), '/') . '/' . ltrim($url, '/');
        } else {
            $endpoint = $url;
        }

        $request->setUri($endpoint);
        $request->setMethod($method);

        if ($data) {
            if ($method == 'GET') {
                $request->setQuery(new Parameters($data));
            } else {
                $request->setPost(new Parameters($data));
            }
        }

        /** @var $response \Zend\Http\Response */
        $response = $this->httpClient->dispatch($request);

        # should interrogate response status, throwing appropriate exceptions for error codes
        return json_decode($response->getBody(), true);
    }

    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function get($url)
    {
        return $this->dispatchRequestAndDecodeResponse($url, "GET");
    }

    public function post($url, $data)
    {
        return $this->dispatchRequestAndDecodeResponse($url, "POST", $data);
    }

    public function put($url, $data)
    {
        return $this->dispatchRequestAndDecodeResponse($url, "PUT", $data);
    }

    public function delete($url)
    {
        return $this->dispatchRequestAndDecodeResponse($url, "DELETE");
    }

    /**
     * @param \Zend\Http\Request $request
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }

    /**
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getHttpClient()
    {
        return $this->httpClient;
    }

}