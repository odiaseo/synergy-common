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

	use CacheAwareQueryTrait;

	/** @var EntityManager */
	private $entityManager;

	/**
	 * @param EntityManagerInterface $entityManager
	 * @param bool                   $enableCache
	 */
	public function __construct( EntityManagerInterface $entityManager, $enableCache = false ) {
		$this->entityManager = $entityManager;
		$this->enableResultCache       = $enableCache;
	}

	/**
	 * Create a QueryBuilder instance
	 *
	 * @return QueryBuilder
	 */
	public function createQueryBuilder() {
		$builder = new QueryBuilder( $this->entityManager );
		$builder->setCachedEnabled( $this->enableResultCache );

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
