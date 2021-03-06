<?php
namespace SynergyCommon\Doctrine;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\ModelTrait\LocaleAwareTrait;
use Laminas\Session\Container;

/**
 * Class QueryBuilder
 *
 * @package SynergyCommon\Doctrine
 */
class QueryBuilder extends DoctrineQueryBuilder implements CacheAwareQueryInterface
{

    const HINT_LINKED_SITES = 'synergy.linked.sites';

    use CacheAwareQueryTrait;

    /**
     * @return \Doctrine\ORM\AbstractQuery
     */
    public function getQuery()
    {
        $container = new Container(LocaleAwareTrait::getNamespace());
        $siteId    = $container->offsetGet(AbstractModel::SESSION_ALLOWED_SITE_KEY);
        $query = $this->setCacheFlag(parent::getQuery());
        $query->setHint(self::HINT_LINKED_SITES, $siteId);

        return $query;
    }

    /**
     * @param mixed $cachedEnabled
     */
    public function setCachedEnabled($cachedEnabled)
    {
        $this->enableResultCache = $cachedEnabled;
    }
}
