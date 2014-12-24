<?php
namespace SynergyCommon\Doctrine;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * Class QueryBuilder
 *
 * @package SynergyCommon\Doctrine
 */
class QueryBuilder extends DoctrineQueryBuilder {

	private $cachedEnabled;

	/**
	 * @return \Doctrine\ORM\Query
	 */
	public function getQuery() {
		$query = parent::getQuery();
		if ( $this->cachedEnabled ) {
			$query->useResultCache( true );
		}

		return $query;
	}

	/**
	 * @param mixed $cachedEnabled
	 */
	public function setCachedEnabled( $cachedEnabled ) {
		$this->cachedEnabled = $cachedEnabled;
	}
}
