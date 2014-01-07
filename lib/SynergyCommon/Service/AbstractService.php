<?php
namespace SynergyCommon\Service;

use SynergyCommon\Entity\AbstractEntity;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\Model\Config\ModelOptions;
use SynergyCommon\Model\Config\SortOrder;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

abstract class AbstractService
    implements ServiceManagerAwareInterface
{
    protected $_entityKey;

    /** @var \Doctrine\ORM\EntityManager */
    protected $_entityManager;

    /** @var  \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_serviceManager = $serviceManager;
    }

    public function processFilters($options = array())
    {
        $config = array();
        if (isset($options['filter'])) {
            $config = $options['filter'];
        }

        return $config;
    }

    public function processSortOrder($options = array())
    {
        $order = array();
        if (isset($options['order'])) {
            foreach ((array)$options['order'] as $orderList) {
                $orderParts = explode(':', $orderList);
                $order[]    = new SortOrder(
                    array(
                         'field'     => $orderParts[0],
                         'direction' => isset($orderParts[1]) ? strtolower($orderParts[1]) : 'asc',
                    )
                );
            }
        }

        return $order;
    }

    public function processFields($options = array())
    {
        $fields = array();

        if ($options) {
            foreach ($options as $key => $value) {
                if (false !== ($model = strstr($key, '_fields', true))) {
                    $fields[$model] = array_map('trim', explode(',', $value));
                }
            }
        }

        return $fields;
    }

    /**
     * Get Model instance
     *
     * @param       $key
     * @param array $options
     * @param array $additionalOptions
     *
     * @return AbstractModel
     */
    public function getModel($key, $options = array(), $additionalOptions = array())
    {
        /** @var $model \SynergyCommon\Model\AbstractModel */
        $model = $this->_serviceManager->get('am\model\\' . $key);
        $model->setOptions(
            new ModelOptions(
                array(
                     'page'      => isset($options['page']) ? $options['page'] : 1,
                     'perPage'   => isset($options['perPage']) ? $options['perPage'] : AbstractModel::PER_PAGE,
                     'filters'   => $this->processFilters($options),
                     'sortOrder' => $this->processSortOrder($options),
                     'fields'    => $this->processFields($options)
                )
            )
        );

        return $model;
    }

    protected function _formatResult($entity, $columns, $key)
    {
        $list = array();

        if ($entity instanceof AbstractEntity) {
            $data = $entity->toArray();
        } else {
            $data = $entity;
        }

        if (isset($columns[$key])) {
            foreach ($data as $field => $value) {
                if (in_array($field, $columns[$key])) {
                    if (is_array($value)) {
                        $list[$field] = $this->_formatResult($value, $columns, $field);
                    } elseif ($value instanceof \DateTime) {
                        // $value->setTimezone(new \DateTimeZone('UTC'));
                        $list[$field] = array(
                            'timestamp' => $value->setTimezone(new \DateTimeZone('UTC'))->getTimestamp(),
                            'timezone'  => $value->getTimezone()->getName(),
                        );
                    } else {
                        $list[$field] = $value;
                    }
                }
            }
        } else {
            $list = $data;
        }

        return $list;
    }


    public function setEntityKey($entityKey)
    {
        $this->_entityKey = $entityKey;
    }

    public function getEntityKey()
    {
        return $this->_entityKey;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    abstract public function getEntityCacheFile();

}