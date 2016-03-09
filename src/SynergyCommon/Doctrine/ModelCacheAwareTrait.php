<?php
namespace SynergyCommon\Doctrine;

use Doctrine\ORM\AbstractQuery;

/**
 * Class ModelCacheAwareTrait
 * @package SynergyCommon\Doctrine
 */
trait ModelCacheAwareTrait
{

    /**
     * Global setting that affects all queries
     *
     * @var bool
     */
    protected $enableResultCache = false;

    /**
     * @param boolean $enabled
     */
    public function setEnableResultCache($enabled)
    {
        $this->enableResultCache = $enabled;
    }

    /**
     * @return boolean
     */
    public function isEnableResultCache()
    {
        return $this->enableResultCache;
    }
}
