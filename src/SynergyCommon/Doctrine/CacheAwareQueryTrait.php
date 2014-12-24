<?php
namespace SynergyCommon\Doctrine;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\ORM\AbstractQuery;

/**
 * Class CacheAwareQueryTrait
 *
 * @package SynergyCommon\Doctrine
 */
trait CacheAwareQueryTrait {

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
	protected $lifetime = 14400;
	/** @var  string */
	protected $cacheKey;

	/**
	 * @param AbstractQuery $query
	 *
	 * @return AbstractQuery
	 */
	public function setCacheFlag( AbstractQuery $query ) {
		if ( $this->enableResultCache ) {
			$query->useResultCache( true, $this->lifetime );
			if ( $this->cacheKey ) {
				$query->setResultCacheId( $this->cacheKey );
			}
		}

		return $this->enableHydrationCacheFlag( $query );
	}

	/**
	 * @param AbstractQuery $query
	 *
	 * @return AbstractQuery
	 */
	protected function enableHydrationCacheFlag( AbstractQuery $query ) {
		if ( $this->enableHydrationCache ) {
			$cacheProfile = new QueryCacheProfile( $this->lifetime );
			$query->setHydrationCacheProfile( $cacheProfile );
			$this->enableHydrationCache = false;
		}

		return $query;
	}

	/**
	 * @param boolean $enabled
	 */
	public function setEnableResultCache( $enabled ) {
		$this->enableResultCache = $enabled;
	}

	/**
	 * @param string $cacheKey
	 */
	public function setCacheKey( $cacheKey ) {
		$this->cacheKey = $cacheKey;
	}

	/**
	 * @param int $lifetime
	 */
	public function setLifetime( $lifetime ) {
		$this->lifetime = $lifetime;
	}
}
