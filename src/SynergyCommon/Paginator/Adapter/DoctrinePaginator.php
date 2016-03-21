<?php
namespace SynergyCommon\Paginator\Adapter;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Tools\Pagination\WhereInWalker;
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
     * @var Query
     */
    private $query;

    /**
     * @var bool
     */
    private $fetchJoinCollection;

    /**
     * @var bool|null
     */
    private $useOutputWalkers;

    /**
     * @var int
     */
    private $count;

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
            $query = $query->getQuery();
        }

        $this->query               = $query;
        $this->useOutputWalkers    = $useOutputWalker;
        $this->fetchJoinCollection = (Boolean)$fetchJoinCollection;
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

    /**
     * Returns the query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns whether the query joins a collection.
     *
     * @return boolean Whether the query joins a collection.
     */
    public function getFetchJoinCollection()
    {
        return $this->fetchJoinCollection;
    }

    /**
     * Returns whether the paginator will use an output walker.
     *
     * @return bool|null
     */
    public function getUseOutputWalkers()
    {
        return $this->useOutputWalkers;
    }

    /**
     * Sets whether the paginator will use an output walker.
     *
     * @param bool|null $useOutputWalkers
     *
     * @return $this
     */
    public function setUseOutputWalkers($useOutputWalkers)
    {
        $this->useOutputWalkers = $useOutputWalkers;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if ($this->count === null) {
            try {
                $this->count = array_sum(array_map('current', $this->getCountQuery()->getScalarResult()));
            } catch (NoResultException $e) {
                $this->count = 0;
            }
        }

        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $offset = $this->query->getFirstResult();
        $length = $this->query->getMaxResults();

        if ($this->fetchJoinCollection) {
            $subQuery = $this->cloneQuery($this->query);

            if ($this->useOutputWalker($subQuery)) {
                $subQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Doctrine\ORM\Tools\Pagination\LimitSubqueryOutputWalker');
            } else {
                $this->appendTreeWalker($subQuery, 'Doctrine\ORM\Tools\Pagination\LimitSubqueryWalker');
            }

            $subQuery->setFirstResult($offset)->setMaxResults($length);

            $ids = array_map('current', $subQuery->getScalarResult());

            $whereInQuery = $this->cloneQuery($this->query);
            // don't do this for an empty id array
            if (count($ids) == 0) {
                return new \ArrayIterator(array());
            }

            $this->appendTreeWalker($whereInQuery, 'Doctrine\ORM\Tools\Pagination\WhereInWalker');
            $whereInQuery->setHint(WhereInWalker::HINT_PAGINATOR_ID_COUNT, count($ids));
            $whereInQuery->setFirstResult(null)->setMaxResults(null);
            $whereInQuery->setParameter(WhereInWalker::PAGINATOR_ID_ALIAS, $ids);
            $whereInQuery->setCacheable($this->query->isCacheable());

            $result = $whereInQuery->getResult($this->query->getHydrationMode());
        } else {
            $result = $this->cloneQuery($this->query)
                ->setMaxResults($length)
                ->setFirstResult($offset)
                ->setCacheable($this->query->isCacheable())
                ->getResult($this->query->getHydrationMode());
        }

        return new \ArrayIterator($result);
    }

    /**
     * Clones a query.
     *
     * @param Query $query The query.
     *
     * @return Query The cloned query.
     */
    private function cloneQuery(Query $query)
    {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;

        $cloneQuery->setParameters(clone $query->getParameters());
        $cloneQuery->setCacheable(false);

        if ($query->getHydrationMode() == AbstractQuery::HYDRATE_OBJECT) {
            //$cloneQuery->setHydrationCacheProfile(null);
        } elseif ($profile = $query->getHydrationCacheProfile()) {
            // $cloneQuery->setHydrationCacheProfile($profile);
        }

        foreach ($query->getHints() as $name => $value) {
            $cloneQuery->setHint($name, $value);
        }

        return $cloneQuery;
    }

    /**
     * Determines whether to use an output walker for the query.
     *
     * @param Query $query The query.
     *
     * @return bool
     */
    private function useOutputWalker(Query $query)
    {
        if ($this->useOutputWalkers === null) {
            return (Boolean)$query->getHint(Query::HINT_CUSTOM_OUTPUT_WALKER) == false;
        }

        return $this->useOutputWalkers;
    }

    /**
     * Appends a custom tree walker to the tree walkers hint.
     *
     * @param Query $query
     * @param string $walkerClass
     */
    private function appendTreeWalker(Query $query, $walkerClass)
    {
        $hints = $query->getHint(Query::HINT_CUSTOM_TREE_WALKERS);

        if ($hints === false) {
            $hints = array();
        }

        $hints[] = $walkerClass;
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, $hints);
    }

    /**
     * Returns Query prepared to count.
     *
     * @return Query
     */
    private function getCountQuery()
    {
        /* @var $countQuery Query */
        $countQuery = $this->cloneQuery($this->query);

        if (!$countQuery->hasHint(CountWalker::HINT_DISTINCT)) {
            $countQuery->setHint(CountWalker::HINT_DISTINCT, true);
        }

        if ($this->useOutputWalker($countQuery)) {
            $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform(); // law of demeter win

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult($platform->getSQLResultCasing('dctrn_count'), 'count');

            $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Doctrine\ORM\Tools\Pagination\CountOutputWalker');
            $countQuery->setResultSetMapping($rsm);
        } else {
            $this->appendTreeWalker($countQuery, 'Doctrine\ORM\Tools\Pagination\CountWalker');
        }

        $countQuery->setFirstResult(null)->setMaxResults(null);

        $parser            = new Parser($countQuery);
        $parameterMappings = $parser->parse()->getParameterMappings();
        /* @var $parameters \Doctrine\Common\Collections\Collection|\Doctrine\ORM\Query\Parameter[] */
        $parameters = $countQuery->getParameters();

        foreach ($parameters as $key => $parameter) {
            $parameterName = $parameter->getName();

            if (!(isset($parameterMappings[$parameterName]) || array_key_exists($parameterName, $parameterMappings))) {
                unset($parameters[$key]);
            }
        }

        $countQuery->setParameters($parameters);

        return $countQuery;
    }
}
