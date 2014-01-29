<?php
namespace SynergyCommon\Model;

use SynergyCommon\Model\AbstractModel;

class SessionModel
    extends AbstractModel
{

    public function collectGabage()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $alias        = $this->getAlias();
        $where        = sprintf('SUM(%s.modified, %s.lifetime) < %d', $alias, $alias, time());

        $query = $queryBuilder->delete()
            ->from($this->getEntity(), $alias)
            ->where($where);

        return $query->getQuery()->execute();
    }
}