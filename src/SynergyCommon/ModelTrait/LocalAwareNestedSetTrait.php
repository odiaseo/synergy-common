<?php
namespace SynergyCommon\ModelTrait;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;

/**
 * Class NestedSetRepository
 *
 * @package AffiliateManager\Model
 */
trait LocalAwareNestedSetTrait
{
    use LocaleAwareTrait;

    /**
     * Gets nodes query
     *
     * @param null $node
     * @param bool $direct
     * @param array $options
     * @param bool $includeNode
     *
     * @return AbstractQuery|Query
     */
    public function getNodesHierarchyQuery(
        $node = null, $direct = false, array $options = array(), $includeNode = false
    )
    {
        $query = parent::getNodesHierarchyQuery($node, $direct, $options, $includeNode);

        return self::addHints($query);
    }

    public function childrenQuery(
        $node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false
    )
    {
        $query = parent::childrenQuery($node, $direct, $sortByField, $direction, $includeNode);

        return self::addHints($query);
    }

    public function getPathQuery($node)
    {
        /** @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->getPathQueryBuilder($node);
        $query        = $queryBuilder->getQuery();

        return self::addHints($query);
    }
}