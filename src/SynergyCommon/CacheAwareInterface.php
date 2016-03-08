<?php
namespace SynergyCommon;

/**
 * Class CacheAwareInterface
 *
 * @package Vaboose
 */
interface CacheAwareInterface
{

    /**
     * @param string $cacheKey
     */
    public function setCache($cacheKey);

    public function getCache();
}