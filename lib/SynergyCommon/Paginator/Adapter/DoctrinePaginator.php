<?php
namespace SynergyCommon\Paginator\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Class DoctrinePaginator
 *
 * @package SynergyCommon\Paginator\Adapter
 */
class DoctrinePaginator extends Paginator implements AdapterInterface
{
    /**
     * @param Query|\Doctrine\ORM\QueryBuilder $query
     * @param bool                             $fetchJoinCollection
     */
    public function __construct($query, $fetchJoinCollection = true)
    {
        if ($query instanceof QueryBuilder) {
            $query->distinct(false);
        }
        parent::__construct($query, $fetchJoinCollection);
        $this->setUseOutputWalkers(true);
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $this->getQuery()->setFirstResult($offset);
        $this->getQuery()->setMaxResults($itemCountPerPage);

        return $this->getIterator();
    }
}