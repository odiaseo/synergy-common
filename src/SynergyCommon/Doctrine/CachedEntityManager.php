<?php
namespace SynergyCommon\Doctrine;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CachedEntityManager
 *
 * @package Application\Doctrine
 */
class CachedEntityManager {

	/** @var EntityManager */
	private $entityManager;
	/** @var bool */
	protected $enabled = false;

	/**
	 * @param EntityManagerInterface $entityManager
	 * @param bool                   $enableCache
	 */
	public function __construct( EntityManagerInterface $entityManager, $enableCache = false ) {
		$this->entityManager = $entityManager;
		$this->enabled       = $enableCache;
	}

	/**
	 * @param AbstractQuery $query
	 *
	 * @return AbstractQuery
	 */
	public function setCacheFlag( AbstractQuery $query ) {
		if ( $this->enabled ) {
			$query->useResultCache( true );
		}

		return $query;
	}

	/**
	 * Create a QueryBuilder instance
	 *
	 * @return QueryBuilder
	 */
	public function createQueryBuilder() {
		$builder = new QueryBuilder( $this->entityManager );
		$builder->setCachedEnabled( $this->enabled );

		return $builder;
	}

	/**
	 * @param $method
	 * @param $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		$return = call_user_func_array( array( $this->entityManager, $method ), $args );
		if ( $return instanceof AbstractQuery ) {
			return $this->setCacheFlag( $return );
		}

		return $return;
	}

}
