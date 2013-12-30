<?php
namespace SynergyCommon\Service;

use SynergyCommon\Entity\BaseEntity;
use SynergyCommon\Exception\InvalidArgumentException;
use Doctrine\ORM\PersistentCollection;

class BaseService
    extends AbstractService
{
    /**
     * Find a enity by ID
     *
     * @param       $id
     * @param array $options
     *
     * @return array
     */
    public function fetchOne($id, $options = array())
    {
        $options['filter'] = array('id' => $id);
        unset($options['page']);
        unset($options['perPage']);

        try {
            $model = $this->getModel($this->getEntityKey(), $options);
            $row   = $model->fetchOne();

            $return = array(
                'error'   => false,
                'message' => '',
                'content' => $this->_formatResult($row, $model->getOptions()->getFields(), $this->getEntityKey())
            );

        } catch (\Exception $exception) {
            $return = array(
                'error'   => true,
                'message' => $exception->getMessage(),
                'content' => null
            );
        }

        return $return;
    }

    /**
     * Return a list of  entities
     *
     * @param array $options
     *
     * @return array
     */
    public function fetchAll($options = array())
    {

        try {
            $offers    = array();
            $model     = $this->getModel($this->getEntityKey(), $options);
            $paginator = $model->getPaginator();
            $rows      = $paginator->getIterator();

            $total   = $paginator->count();
            $rowNum  = $paginator->getQuery()->getMaxResults();
            $columns = $model->getOptions()->getFields();

            foreach ($rows as $row) {
                $offers[] = $this->_formatResult($row, $columns, $this->getEntityKey());
            }
            $return = array(
                'error'   => false,
                'message' => $total ? '' : sprintf('No %s found', $this->getEntityKey()),
                'content' => array(
                    'page'      => $model->getOptions()->getPage(),
                    'pageTotal' => ceil($total / $rowNum),
                    'perPage'   => $model->getOptions()->getPerPage(),
                    'total'     => $total,
                    'rows'      => $offers
                )
            );


        } catch (\Exception $exception) {
            $return = array(
                'error'   => true,
                'message' => $exception->getMessage(),
                'content' => null
            );
        }

        return $return;
    }

    /**
     * Update an existing entity
     *
     * @param $id
     * @param $data
     *
     * @return array
     * @throws \SynergyCommon\Exception\InvalidArgumentException
     */
    public function update($id, $data)
    {

        try {
            $model = $this->getModel($this->getEntityKey());

            /** @var $offer \SynergyCommon\Entity\AbstractEntity */
            $offer = $model->findObject($id);
            if ($offer) {
                $offer = $model->populateEntity($offer, $data);
                $offer = $model->save($offer);

                $return            = $this->fetchOne($id, $data);
                $return['message'] = sprintf(
                    '%s #%d successfully updated', ucfirst($this->getEntityKey()), $offer->getId()
                );
            } else {
                throw new InvalidArgumentException(sprintf(
                        '%s with ID #%d was not found', ucfirst($this->getEntityKey()), $id
                    )
                );
            }
        } catch (\Exception $exception) {
            $return = array(
                'error'   => true,
                'message' => $exception->getMessage(),
                'content' => null
            );
        }

        return $return;
    }

    /**
     * List associations
     *
     * @param $entityKey
     * @param $entityId
     * @param $subEntity
     *
     * @return array
     * @throws \SynergyCommon\Exception\InvalidArgumentException
     */
    public function listAssociation($entityKey, $entityId, $subEntity)
    {
        try {
            $rows = array();

            /** @var $model \SynergyCommon\Model\AbstractModel */
            $model  = $this->getModel($entityKey);
            $entity = $model->findObject($entityId);

            $method = 'get' . ucfirst($subEntity);
            $items  = $entity->$method();

            if ($items instanceof PersistentCollection) {
                /** @var \SynergyCommon\Entity\BaseEntity $row */
                foreach ($items as $row) {
                    $rows[$subEntity][] = $row->getId();
                }
            } else {
                throw new InvalidArgumentException('Invalid association found. Association is not MANY-to-ONE or ONE-to-MANY');
            }

            $total  = count($rows);
            $return = array(
                'error'   => false,
                'message' => $total
                    ? ''
                    : sprintf(
                        'No %s associated with %s ID #%d', $subEntity, $entityKey, $entityId
                    ),
                'content' => $rows
            );

        } catch (\Exception $exception) {
            $return = array(
                'error'   => true,
                'message' => $exception->getMessage(),
                'content' => null
            );
        }

        return $return;

    }

    public function associateEntity($entityKey, $entityId, $subEntity, $ids = '')
    {
        try {
            $options = array($subEntity . '_fields' => $ids);

            /** @var $model \SynergyCommon\Model\AbstractModel */
            $model  = $this->getModel($entityKey, $options);
            $entity = $model->findObject($entityId);
            $entity = $model->populateEntity($entity, $model->getOptions()->getFields());
            $entity = $model->save($entity);

            $return = $this->listAssociation($entityKey, $entityId, $subEntity);

            if (!$return['error']) {
                $return['message'] = sprintf(
                    '%s #%d successfully updated', ucfirst($this->getEntityKey()), $entity->getId()
                );
            }

        } catch (\Exception $exception) {
            $return = array(
                'error'   => true,
                'message' => $exception->getMessage(),
                'content' => null
            );
        }

        return $return;

    }


    public function deleteAssociation($entityKey, $entityId, $subEntity)
    {
        $return = $this->associateEntity($entityKey, $entityId, $subEntity);

        if (!$return['error']) {

            $return['mwssage'] = sprintf(
                '%s associated with %s ID #%d successfully deleted', $subEntity, $entityKey, $entityId
            );
        }

        return $return;
    }

    /**
     * Create a new entity
     *
     * @param $data
     *
     * @return array
     */
    public function create($data)
    {
        try {
            $model       = $this->getModel($this->getEntityKey());
            $entityClass = $model->getEntity();

            /** @var $offer \SynergyCommon\Entity\AbstractEntity */
            $offer = new $entityClass();
            $offer = $model->populateEntity($offer, $data);

            $offer = $model->save($offer);

            $return = array(
                'error'   => false,
                'message' => sprintf('%s #%d successfully created', ucfirst($this->getEntityKey()), $offer->getId()),
                'content' => $this->_formatResult($offer, $model->getOptions()->getFields(), $this->getEntityKey())
            );

        } catch (\Exception $exception) {
            $return = array(
                'error'   => true,
                'message' => $exception->getMessage(),
                'content' => null
            );
        }

        return $return;
    }

    /**
     * Delete entity by ID
     *
     * @param $id
     *
     * @return array
     */
    public function delete($id)
    {
        try {
            $model = $this->getModel($this->getEntityKey());
            $model->remove($id);

            $return = array(
                'error'   => false,
                'message' => sprintf('%s #%d successfully deleted', ucfirst($this->getEntityKey()), $id)
            );

        } catch (\Exception $exception) {
            $return = array(
                'error'   => true,
                'message' => $exception->getMessage(),
            );
        }

        return $return;
    }
}