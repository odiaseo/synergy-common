<?php
namespace SynergyCommon\Paginator\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SynergyCommon\Paginator\IdentityProviderInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Class DoctrinePaginator
 *
 * @package SynergyCommon\Paginator\Adapter
 */
class DoctrinePaginator extends Paginator implements AdapterInterface, IdentityProviderInterface
{
    /**
     * @param Query|QueryBuilder $query
     * @param bool $fetchJoinCollection
     * @param bool $useOutputWalker
     * @param bool $distinct
     */

    public function __construct($query, $fetchJoinCollection = false, $useOutputWalker = false, $distinct = false)
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

    /**
     * Generates a string of currently query to use for the cache second level cache.
     *
     * @return string
     */
    public function getUniqueIdentifier()
    {
        $query  = $this->getQuery();
        $sql    = $query->getSQL();
        $hints  = $query->getHints();
        $params = array_map(function (Parameter $parameter) use ($query) {
            // Small optimization
            // Does not invoke processParameterValue for scalar values
            if (is_scalar($value = $parameter->getValue())) {
                return $value;
            }

            return $query->processParameterValue($value);
        }, $query->getParameters()->getValues());

        ksort($hints);

        return sha1($sql . '-' . serialize($params) . '-' . serialize($hints));
    }
}
