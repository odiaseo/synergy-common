<?php
namespace SynergyCommon\Doctrine;

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

	public function __construct( EntityManagerInterface $entityManager, $enableCache = false ) {
		$this->entityManager = $entityManager;
		$this->enabled       = $enableCache;
	}

	/**
	 * @param string $dql
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function createQuery( $dql = '' ) {
		$query = $this->entityManager->createQuery( $dql );
		if ( $this->enabled ) {
			$query->useResultCache( true );
		}

		return $query;
	}

	/**
	 * @param $method
	 * @param $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		return call_user_func_array( array( $this->entityManager, $method ), $args );
	}
}
