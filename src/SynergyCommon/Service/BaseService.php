<?php
namespace SynergyCommon\Service;

use Doctrine\ORM\PersistentCollection;
use SynergyCommon\Exception\InvalidArgumentException;
use SynergyCommon\PrefixAwareInterface;
use Laminas\Filter\Word\CamelCaseToDash;

/**
 * Class BaseService
 * @package SynergyCommon\Service
 */
class BaseService extends AbstractService
{
    /** @var \SynergyCommon\Util\ErrorHandler */
    protected $logger;

    protected static $checked;

    /**
     * Find a enity by ID
     *
     * @param       $entityId
     * @param array $options
     *
     * @return array
     */
    public function fetchOne($entityId, $options = array())
    {
        $options['filter'] = array('id' => $entityId);
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
                throw new InvalidArgumentException(
                    sprintf(
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
                throw new InvalidArgumentException(
                    'Invalid association found. Association is not MANY-to-ONE or ONE-to-MANY'
                );
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
                    '%s #%d successfully updated', ucfirst($this->getEntityKey()), $entity ? $entity->getId() : ''
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
            /** @var $offer \SynergyCommon\Entity\AbstractEntity */
            $model       = $this->getModel($this->getEntityKey());
            $entityClass = $model->getEntity();
            $offer       = new $entityClass();
            $offer       = $model->populateEntity($offer, $data);

            $offer = $model->save($offer);

            if ($offer) {
                $return = array(
                    'error'   => false,
                    'message' => sprintf(
                        '%s #%d successfully created', ucfirst($this->getEntityKey()),
                        $offer->getId()
                    ),
                    'content' => $this->_formatResult($offer, $model->getOptions()->getFields(), $this->getEntityKey())
                );
            } else {
                $return = array(
                    'error'   => true,
                    'code'    => 400,
                    'message' => sprintf(
                        'Could not create entity of type %s', ucfirst($this->getEntityKey())
                    ),
                    'content' => $data
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

    public function getEntityCacheFile($force =false)
    {
        $config   = $this->getServiceLocator()->get('Config');
        $filename = $config['synergy']['entity_cache']['orm'];

        if ($force or !empty($config['synergy']['check_entity_cache_file'])) {

            if (!is_readable($filename)) {
                $refresh = true;
            } else {
                $lifetime = $config['synergy']['entity_cache_lifetime'];
                $fileTime = (int)filemtime($filename);
                $refresh  = ((time() - $fileTime) > $lifetime);
            }

            if ($refresh) {
                $this->createEntityCache($filename);
            }
        }

        return $filename;
    }

    /**
     * Create cache file if it does not exist
     *
     * @param                             $filename
     *
     * @return bool|int
     */
    public function createEntityCache($filename)
    {
        /** @var $entityManager  \Doctrine\ORM\EntityManager */
        $output = array();
        $config = $this->getServiceLocator()->get('Config');
        foreach ($config['doctrine']['connection'] as $orm => $data) {
            $ormAlias = 'doctrine.entitymanager.' . $orm;
            if ($this->getServiceLocator()->has($ormAlias)) {
                $entityManager = $this->getServiceLocator()->get($ormAlias);
                $cmf           = $entityManager->getMetadataFactory();
                $classes       = $cmf->getAllMetadata();
                $filter        = new CamelCaseToDash();

                /** @var \Doctrine\ORM\Mapping\ClassMetadata $class */
                foreach ($classes as $class) {
                    $className       = $class->getName();
                    $nameList[] = $className;
                    $reflectionClass = new \ReflectionClass($className);
                    if (!($reflectionClass->isAbstract() || $class->isMappedSuperclass)) {
                        $name   = str_replace($class->namespace . '\\', '', $className);
                        $entity = new $className;
                        if ($entity instanceof PrefixAwareInterface and ($prefix = $entity->getPrefix())) {
                            $name = trim($prefix, '-') . '-' . $name;
                        }

                        $key          = strtolower($filter->filter($name));
                        $output[$key] = $className;
                    }else{
                        $notAdded = $className;
                    }
                }
            }
        }

        ksort($output);
        asort($nameList);
        $data = '<?php return ' . var_export($output, true) . ';';

        if (!is_dir(dirname($filename))) {
            @mkdir(dirname($filename), 0755, true);
        }

        return file_put_contents($filename, $data);
    }

    /**
     * Get key from class name map
     *
     * @param $className
     *
     * @return mixed
     */
    public function getEntityKeyFromClassname($className)
    {
        $filename = $this->getEntityCacheFile();
        $cache    = include "$filename";

        return array_search($className, $cache);
    }

    /**
     * Get classname from key map
     *
     * @param $entityKey
     *
     * @return null
     */
    public function getClassnameFromEntityKey($entityKey)
    {
        $entityKey = strtolower($entityKey);
        $filename  = $this->getEntityCacheFile();
        if (file_exists($filename)) {
            $cache = include "$filename";

            return isset($cache[$entityKey]) ? $cache[$entityKey] : null;
        }
        return null;
    }

    /**
     * @return \SynergyCommon\Util\ErrorHandler
     */
    public function getLogger()
    {
        return $this->getServiceLocator()->get('logger');
    }

    /**
     * @param \SynergyCommon\Util\ErrorHandler $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return BaseApiService
     */
    public function getApiService()
    {
        if ($this->getServiceLocator()->has('common\api\service')) {
            return $this->getServiceLocator()->get('common\api\service');
        }

        return null;
    }
}
