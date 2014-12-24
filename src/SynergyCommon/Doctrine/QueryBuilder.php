<?php
namespace SynergyCommon\Doctrine;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * Class QueryBuilder
 *
 * @package SynergyCommon\Doctrine
 */
class QueryBuilder extends DoctrineQueryBuilder {

	use CacheAwareQueryTrait;

	/**
	 * @return \Doctrine\ORM\Query
	 */
	public function getQuery() {
		return $this->setCacheFlag( parent::getQuery() );
	}

	/**
	 * @param mixed $cachedEnabled
	 */
	public function setCachedEnabled( $cachedEnabled ) {
		$this->enableResultCache = $cachedEnabled;
	}
}
