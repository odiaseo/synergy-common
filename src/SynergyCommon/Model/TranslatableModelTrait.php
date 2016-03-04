<?php
namespace SynergyCommon\Model;

use SynergyCommon\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

/**
 * Class TranslatableModelTrait
 *
 * @package Vaboose\Model
 */
trait TranslatableModelTrait
{
    /**
     * @param        $locale
     * @param int $page
     * @param int $perPage
     * @param string $orderBy
     *
     * @return Paginator
     */
    public function getTranslatableRecords($locale, $page = 1, $perPage = 15, $orderBy = '')
    {
        /** @var $this self | AbstractModel */
        $builder = $this->getEntityManager()->createQueryBuilder();
        $query   = $builder->select('e,t')
            ->from($this->getEntity(), 'e')
            ->leftJoin('e.translations', 't')
            ->andWhere('t.locale = :locale')
            ->setParameters(
                [
                    ':locale' => $locale
                ]
            );

        if ($orderBy) {
            $query->orderBy('e.' . $orderBy);
        }

        $adapter = new DoctrinePaginator($query);
        $pager   = new Paginator($adapter);

        $pager->setCurrentPageNumber($page);
        $pager->setItemCountPerPage($perPage);
        $pager->setPageRange(25);

        return $pager;
    }

    /**
     * @param $entity
     * @param $transId
     * @param $field
     * @param $content
     *
     * @return mixed
     */
    public function updateTranslation($entity, $transId, $field, $content)
    {
        /** @var $this self | AbstractModel */
        $builder = $this->getEntityManager()->createQueryBuilder();
        $query   = $builder->update($entity, 'e')
            ->set('e.content', ':content')
            ->where('e.id = :id')
            ->andWhere("e.field = :field")
            ->setParameters(
                [
                    ':id'      => $transId,
                    ':field'   => $field,
                    ':content' => $content
                ]
            );

        return $query->getQuery()->execute();
    }

    /**
     * @param $entity
     * @param $transId
     * @param $field
     *
     * @return mixed
     */
    public function getFieldTranslations($entity, $transId, $field)
    {
        /** @var $this self | AbstractModel */
        $builder = $this->getEntityManager()->createQueryBuilder();
        $query   = $builder->select(['e.content', 'e.locale', 'e.id'])
            ->from($entity, 'e')
            ->where('e.object = :id')
            ->andWhere('e.field = :field')
            ->setParameters(
                [
                    ':id'    => $transId,
                    ':field' => $field,
                ]
            );

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param $entity
     * @param $transId
     * @param $field
     *
     * @return mixed
     */
    public function getTranslatedContent($entity, $transId, $field)
    {
        /** @var $this self | AbstractModel */
        $builder = $this->getEntityManager()->createQueryBuilder();
        $query   = $builder->select('e.content')
            ->from($entity, 'e')
            ->where('e.id = :id')
            ->andWhere("e.field = :field")
            ->setParameters(
                [
                    ':id'    => $transId,
                    ':field' => $field,
                ]
            );

        return $query->getQuery()->getSingleScalarResult();
    }
}
