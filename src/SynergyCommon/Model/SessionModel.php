<?php
namespace SynergyCommon\Model;

/**
 * Class SessionModel
 *
 * @package SynergyCommon\Model
 */
class SessionModel extends AbstractModel
{
    /**
     * @return mixed
     */
    public function collectGabbage()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $alias        = $this->getAlias();
        $query        = $queryBuilder->delete()
            ->from($this->getEntity(), $alias)
            ->where($queryBuilder->expr()->lt($alias . '.expireBy', time()));

        return $query->getQuery()->execute();
    }

    /**
     * @param $sessionId
     * @param $name
     *
     * @return mixed
     */
    public function getSessionRecord($sessionId, $name)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $query        = $queryBuilder->select('e')
            ->from($this->getEntity(), 'e')
            ->where('e.sessionId = :id')
            ->andWhere('e.name = :name')
            ->setParameters(
                array(
                    'id'   => $sessionId,
                    'name' => $name
                )
            )->setMaxResults(1)
            ->getQuery();
        $query->useResultCache(false);

        return $query->getOneOrNullResult();
    }

    /**
     * @param $sessionId
     * @param $sessionName
     *
     * @return bool|mixed
     */
    public function deleteSession($sessionId, $sessionName)
    {
        try {
            $queryBuilder = $this->getEntityManager()->createQueryBuilder();
            $query        = $queryBuilder->delete()
                ->from($this->getEntity(), 'e')
                ->where('e.sessionId = :id')
                ->andWhere('e.name = :name')
                ->setParameter(':name', $sessionName)
                ->setParameter(':id', $sessionId);

            return $query->getQuery()->execute();
        } catch (\Exception $exception) {
            $this->getLogger()->logException($exception);

            return false;
        }
    }
}
