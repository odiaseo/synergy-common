<?php
namespace SynergyCommon\Paginator\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Zend\Paginator\Adapter\AdapterInterface;

class DoctrinePaginator
    extends Paginator
    implements AdapterInterface
{
    /**
     * @param Query|\Doctrine\ORM\QueryBuilder $query
     * @param bool                             $fetchJoinCollection
     */
    public function __construct($query, $fetchJoinCollection = false)
    {
        if ($query instanceof QueryBuilder) {
            $query->distinct(false);
        }
        parent::__construct($query, false);
        $this->setUseOutputWalkers(false);
    }

    public function getItems($offset, $itemCountPerPage)
    {
        return $this->getIterator();
    }
}