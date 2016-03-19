<?php
namespace SynergyCommon\Doctrine;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Cache\QueryCacheProfile as DoctrineProfile;

/**
 * Class QueryCacheProfile
 * @package SynergyCommon\Doctrine
 */
class QueryCacheProfile extends DoctrineProfile
{
    /**
     * @var \Doctrine\Common\Cache\Cache|null
     */
    private $resultCacheDriver;

    /**
     * @var integer
     */
    private $lifetime = 0;

    /**
     * @var string|null
     */
    private $cacheKey;

    /**
     * @param integer $lifetime
     * @param string|null $cacheKey
     * @param \Doctrine\Common\Cache\Cache|null $resultCache
     */
    public function __construct($lifetime = 0, $cacheKey = null, Cache $resultCache = null)
    {
        $this->lifetime          = $lifetime;
        $this->cacheKey          = $cacheKey;
        $this->resultCacheDriver = $resultCache;
    }

    /**
     * Generates the real cache key from query, params and types.
     *
     * @param string $query
     * @param array $params
     * @param array $types
     *
     * @return array
     */
    public function generateCacheKeys($query, $params, $types)
    {
        $realCacheKey = hash('sha512', $query . "-" . serialize($params) . "-" . serialize($types));
        // should the key be automatically generated using the inputs or is the cache key set?
        if ($this->cacheKey === null) {
            $cacheKey = sha1($realCacheKey);
        } else {
            $cacheKey = $this->cacheKey;
        }

        return array($cacheKey, $realCacheKey);
    }

    /**
     * @return Cache|null
     */
    public function getResultCacheDriver()
    {
        return $this->resultCacheDriver;
    }

    /**
     * @param Cache $resultCacheDriver
     * @return $this
     */
    public function setResultCacheDriver(Cache $resultCacheDriver)
    {
        $this->resultCacheDriver = $resultCacheDriver;
        return $this;
    }

    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @param int $lifetime
     * @return $this
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * @param null|string $cacheKey
     * @return $this
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }
}
