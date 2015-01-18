<?php
namespace SynergyCommon\Doctrine;

use Doctrine\ORM\AbstractQuery;

/**
 * Class CacheAwareQueryTrait
 *
 * @package SynergyCommon\Doctrine
 */
interface CacheAwareQueryInterface
{

    /**
     * @param AbstractQuery $query
     *
     * @return AbstractQuery
     */
    public function setCacheFlag(AbstractQuery $query);

    /**
     * @param boolean $enabled
     */
    public function setEnableResultCache($enabled);

    /**
     * @param string $cacheKey
     */
    public function setCacheKey($cacheKey);

    /**
     * @param int $lifetime
     */
    public function setLifetime($lifetime);

    /**
     * @param boolean $enableHydrationCache
     */
    public function setEnableHydrationCache($enableHydrationCache);
}
