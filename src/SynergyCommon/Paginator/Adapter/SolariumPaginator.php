<?php

namespace SynergyCommon\Paginator\Adapter;

use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Solarium result paginator
 *
 * @license MIT
 */
class SolariumPaginator implements AdapterInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var int
     */
    protected $count;

    /**
     * Solr Core endpoint
     *
     * @var string
     */
    protected $endpoint = null;

    /**
     * Post processing callback funciton
     *
     * @var null
     */
    protected $_callback;

    /** @var \Solarium\QueryType\Select\Result\Result */
    protected $_solrResult;

    public function __construct(Client $client, Query $query, $endpoint = null, $callback = null)
    {
        $this->client    = $client;
        $this->query     = $query;
        $this->endpoint  = $endpoint;
        $this->_callback = $callback;
    }

    public function count()
    {
        if (null === $this->count) {
            $this->getItems(0, 0);
        }

        return $this->count;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $this->query->setStart($offset);
        $this->query->setRows($itemCountPerPage);
        $result      = $this->client->select($this->query, $this->endpoint);
        $this->count = $result->getNumFound();

        if ($itemCountPerPage and $this->_callback) {
            $this->_solrResult = $result;
            $result            = call_user_func($this->_callback, $result);
        }

        return $result;
    }

    /**
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function getSolrResult()
    {
        return $this->_solrResult;
    }
}
