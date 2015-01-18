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
     * @param Query|QueryBuilder $query
     * @param bool               $fetchJoinCollection
     * @param bool               $useOutputWalker
     * @param bool               $distinct
     */
    public function __construct($query, $fetchJoinCollection = true, $useOutputWalker = false, $distinct = false)
    {
        if ($query instanceof QueryBuilder) {
            $query->distinct($distinct);
        }
        parent::__construct($query, $fetchJoinCollection);
        $this->setUseOutputWalkers($useOutputWalker);
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $this->getQuery()->setFirstResult($offset);
        $this->getQuery()->setMaxResults($itemCountPerPage);

        return $this->getIterator();
    }
}
