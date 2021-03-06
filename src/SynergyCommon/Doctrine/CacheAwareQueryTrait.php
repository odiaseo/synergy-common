<?php
namespace SynergyCommon\Doctrine;

use Doctrine\ORM\AbstractQuery;

/**
 * Class CacheAwareQueryTrait
 *
 * @package SynergyCommon\Doctrine
 */
trait CacheAwareQueryTrait
{
    /**
     * Global setting that affects all queries
     *
     * @var bool
     */
    protected $enableResultCache = false;
    /**
     * Set on query by query basis
     *
     * @var bool
     */
    protected $enableHydrationCache = false;

    /** @var int */
    protected $cacheLifetime = 14400;
    /** @var  string */
    protected $cacheKey;

    /**
     * @param AbstractQuery $query
     *
     * @return AbstractQuery
     */
    public function setCacheFlag(AbstractQuery $query)
    {
        if ($this->enableResultCache) {
            $this->cloneQueryCacheProfile($query);
            $query->useResultCache(true, $this->cacheLifetime);
            if ($this->cacheKey) {
                $query->setResultCacheId($this->cacheKey);
            }
        }

        return $this->enableHydrationCacheFlag($query);
    }

    /**
     * @param AbstractQuery $query
     *
     * @return AbstractQuery
     */
    protected function enableHydrationCacheFlag(AbstractQuery $query)
    {
        if ($this->enableHydrationCache) {
            $driver           = $query->getEntityManager()->getConfiguration()->getHydrationCacheImpl();
            $hydrationProfile = new QueryCacheProfile($this->cacheLifetime, null, $driver);

            $query->setHydrationCacheProfile($hydrationProfile);
            $this->enableHydrationCache = false;
        }

        return $query;
    }

    protected function cloneQueryCacheProfile(AbstractQuery $query)
    {
        $profile = $query->getQueryCacheProfile();

        if (!$profile) {
            $driver = $query->getEntityManager()->getConfiguration()->getResultCacheImpl();
        } else {
            $driver = $profile->getResultCacheDriver();
        }

        if (!$profile or !$profile instanceof QueryCacheProfile) {
            $profile = new QueryCacheProfile($this->cacheLifetime, null, $driver);
            $query->setResultCacheProfile($profile);
        }
    }

    /**
     * @param boolean $enabled
     */
    public function setEnableResultCache($enabled)
    {
        $this->enableResultCache = $enabled;
    }

    /**
     * @param string $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * @param int $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->cacheLifetime = $lifetime;
    }

    /**
     * @param boolean $enableHydrationCache
     */
    public function setEnableHydrationCache($enableHydrationCache)
    {
        $this->enableHydrationCache = $enableHydrationCache;
    }

    /**
     * @return boolean
     */
    public function isEnableResultCache()
    {
        return $this->enableResultCache;
    }
}
