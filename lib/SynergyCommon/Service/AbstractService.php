<?php
namespace SynergyCommon\Service;

use SynergyCommon\Entity\AbstractEntity;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\Model\Config\ModelOptions;
use SynergyCommon\Model\Config\SortOrder;
use Zend\Console\ColorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

abstract class AbstractService
    implements ServiceManagerAwareInterface
{
    /**
     * Print to console
     *
     * @var bool
     */
    protected $_verbose = false;

    protected $_entityKey;

    /** @var \Doctrine\ORM\EntityManager */
    protected $_entityManager;

    /** @var  \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    /** @var \SynergyCommon\Util\ErrorHandler */
    protected $_logger;

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
        $model = $this->getServiceManager()->get('am\model\\' . $key);
        $model->setOptions(
            new ModelOptions(
                array(
                     'page'      => isset($options['page']) ? $options['page'] : 1,
                     'perPage'   => isset($options['perPage']) ? $options['perPage'] : AbstractModel::PER_PAGE,
                     'filters'   => $this->processFilters($options),
                     'sortOrder' => $this->processSortOrder($options),
                     'fields'    => $this->processFields($options),
                     'data'      => $additionalOptions
                )
            )
        );

        return $model;
    }

    protected function _formatResult($entity, $columns, $key)
    {
        $list = array();

        if ($entity instanceof AbstractEntity) {
            if (isset($columns[$key])) {
                foreach ($columns[$key] as $field) {
                    $method = 'get' . ucfirst($field);
                    if (method_exists($entity, $method)) {
                        $list[$field] = $entity->{$method}();
                    }

                    if ($field == 'deeplink' and method_exists($entity, 'formatDeeplink')) {
                        $list[$field] = $entity->formatDeeplink($this->getServiceManager());
                    }
                }
            } else {
                $list = $entity->toArray();
            }
        } else {
            $data = $entity;
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
        if (!$this->_entityManager) {
            $this->_entityManager = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
        }

        return $this->_entityManager;
    }

    /**
     * Print message to console
     *
     * @param      $msg
     * @param int  $repeat
     * @param bool $lineBreak
     * @param int  $color
     * @param null $bgColor
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printMessage($msg, $repeat = 1, $lineBreak = true, $color = ColorInterface::GRAY, $bgColor = null)
    {
        /** @var $console \Zend\Console\Adapter\Windows */
        $console = $this->getServiceManager()->get('console');
        if ($this->getVerbose()) {
            $msg  = is_array($msg) ? print_r($msg, true) : $msg;
            $sign = $repeat ? str_repeat("\t", $repeat) . ' ' : '';
            $msg  = "{$sign}$msg";
            if ($lineBreak) {
                $console->writeLine($msg, $color, $bgColor);
            } else {
                $console->write($msg, $color, $bgColor);
            }
        }

        return $console;
    }

    protected function _filterHostName($host)
    {
        return str_replace(array('http://', 'https://', 'www.'), '', $host);
    }

    /**
     * @param boolean $verbose
     */
    public function setVerbose($verbose)
    {
        $this->_verbose = $verbose;
    }

    /**
     * @return boolean
     */
    public function getVerbose()
    {
        return $this->_verbose;
    }

    /**
     * @param \SynergyCommon\Util\ErrorHandler $logger
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @return \SynergyCommon\Util\ErrorHandler
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    abstract public function getEntityCacheFile();

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->_serviceManager;
    }

}
