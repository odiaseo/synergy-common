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
     * @param     $locale
     * @param int $page
     * @param int $perPage
     *
     * @return Paginator
     */
    public function getTranslatableRecords($locale, $page = 1, $perPage = 15)
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
}
