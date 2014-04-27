<?php
namespace SynergyCommon\Model\Config;

use Doctrine\ORM\AbstractQuery;
use Zend\Stdlib\AbstractOptions;

class ModelOptions
    extends AbstractOptions
{
    /**
     * Additional ata
     *
     * @var array
     */
    protected $_data;

    protected $_resultCallback;
    /**
     * Solr Core
     *
     * @var string
     */
    protected $_core;
    /**
     * Search term for solar
     *
     * @var string
     */
    protected $_searchTerm;
    /**
     * Maximum solr search term
     *
     * @var int
     */
    protected $_wordLimit = 4;
    /**
     * @var int
     */
    protected $_hydrationMode = AbstractQuery::HYDRATE_OBJECT;
    /**
     * Result per page
     *
     * @var int
     */
    protected $_perPage;
    /**
     * Page number
     *
     * @var int
     */
    protected $_page;
    /**
     * Filters for where clauses
     *
     * @var array
     */
    protected $_filters;
    /**
     * Sort Order
     *
     * @var array
     */
    protected $_sortOrder;
    /**
     * Fields to retrun
     *
     * @var array
     */
    protected $_fields;

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->_filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->_page = $page;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->_perPage = $perPage;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->_perPage;
    }

    /**
     * @param array $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->_sortOrder = $sortOrder;
    }

    /**
     * @return array
     */
    public function getSortOrder()
    {
        return $this->_sortOrder;
    }

    /**
     * @param int $hydrationMode
     */
    public function setHydrationMode($hydrationMode)
    {
        $this->_hydrationMode = $hydrationMode;
    }

    /**
     * @return int
     */
    public function getHydrationMode()
    {
        return $this->_hydrationMode;
    }

    /**
     * @param string $core
     */
    public function setCore($core)
    {
        $this->_core = $core;
    }

    /**
     * @return string
     */
    public function getCore()
    {
        return $this->_core;
    }

    /**
     * @param string $searchTerm
     */
    public function setSearchTerm($searchTerm)
    {
        $this->_searchTerm = $searchTerm;
    }

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->_searchTerm;
    }

    /**
     * @param int $wordLimit
     */
    public function setWordLimit($wordLimit)
    {
        $this->_wordLimit = $wordLimit;
    }

    /**
     * @return int
     */
    public function getWordLimit()
    {
        return $this->_wordLimit;
    }

    public function setResultCallback($proxyModel)
    {
        $this->_resultCallback = $proxyModel;
    }

    public function getResultCallback()
    {
        return $this->_resultCallback;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}