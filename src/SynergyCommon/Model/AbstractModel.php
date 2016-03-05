<?php
namespace SynergyCommon\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SynergyCommon\Doctrine\CacheAwareQueryInterface;
use SynergyCommon\Doctrine\CacheAwareQueryTrait;
use SynergyCommon\Doctrine\QueryBuilder;
use SynergyCommon\Entity\AbstractEntity;
use SynergyCommon\Exception\InvalidArgumentException;
use SynergyCommon\Exception\InvalidEntityException;
use SynergyCommon\Model\Config\ModelOptions;
use SynergyCommon\ModelTrait\LocaleAwareTrait;
use SynergyCommon\NestedsetInterface;
use SynergyCommon\Paginator\Adapter\DoctrinePaginator;
use SynergyCommon\Util;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Session\Container;

/**
 * Class AbstractModel
 *
 * @method setEnableHydrationCache()
 * @method setEnableResultCache()
 * @method \SynergyCommon\Doctrine\CachedEntityManager | \Doctrine\ORM\EntityManager getEntityManager()
 * @package SynergyCommon\Model
 */
class AbstractModel implements NestedsetInterface, CacheAwareQueryInterface, ServiceLocatorAwareInterface
{

    use CacheAwareQueryTrait;
    use ServiceLocatorAwareTrait;

    const EQUAL                 = 'eq';
    const NOT_EQUAL             = 'ne';
    const LESS_THAN             = 'lt';
    const LESS_THAN_OR_EQUAL    = 'lte';
    const GREATER_THAN          = 'gt';
    const GREATER_THAN_OR_EQUAL = 'gte';
    const BEGIN_WITH            = 'bw';
    const LIKE                  = 'lk';
    const NOT_BEGIN_WITH        = 'nb';
    const END_WITH              = 'ew';
    const NOT_END_WITH          = 'en';
    const CONTAIN               = 'cn';
    const NOT_CONTAIN           = 'nc';
    const IN                    = 'in';
    const NOT_IN                = 'ni';

    const DEFAULT_EXPRESSION = self::EQUAL;

    const PER_PAGE            = 15;
    const INDEX_PER_PAGE      = 50;
    const DB_DATE_FORMAT      = 'Y-m-d H:i:s';
    const DB_DATE_ONLY_FORMAT = 'Y-m-d';
    const SESSION_LOCALE_KEY  = 'active_locale';
    const SESSION_SITE_KEY    = 'active_site';

    const FILTER_SESSION_KEY = 'disableQueryFilter';

    protected $fields
        = [
            'id',
            'title',
            'slug'
        ];

    /**
     * Mapping human-readable constants to DQL operatores
     *
     * @var array
     */
    protected $_operator
        = array(
            self::EQUAL                 => '= ?',
            self::NOT_EQUAL             => '!= ?',
            self::LESS_THAN             => '< ?',
            self::LESS_THAN_OR_EQUAL    => '<= ?',
            self::GREATER_THAN          => '> ?',
            self::GREATER_THAN_OR_EQUAL => '>= ?',
            self::BEGIN_WITH            => 'LIKE ?',
            self::LIKE                  => 'LIKE ?',
            self::NOT_BEGIN_WITH        => 'NOT LIKE ?',
            self::END_WITH              => 'LIKE ?',
            self::NOT_END_WITH          => 'NOT LIKE ?',
            self::CONTAIN               => 'LIKE ?',
            self::NOT_CONTAIN           => 'NOT LIKE ?',
            self::IN                    => 'IN ?',
            self::NOT_IN                => 'NOT IN ?'
        );

    /** @var \SynergyCommon\Util\ErrorHandler |\Zend\Log\Logger */
    protected $logger;
    /**
     * @var string
     */
    protected $_alias = 'e';
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $_qb;

    /** @var string */
    protected $orm = 'orm_default';

    /** @var \Doctrine\ORM\EntityManager */
    protected $_entityManager;

    /** @var \SynergyCommon\Model\Config\ModelOptions */
    protected $_options;

    /** @var string */
    protected $_entity;

    /** @var string */
    protected $_entityKey;

    /**
     * @var object
     */
    protected $_identity;

    protected static $_site;
    /**
     * Access control
     *
     * @var
     */
    protected $_acl;

    public function setEntity($entity)
    {
        $this->_entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    public function setEntityManager($em)
    {
        $this->_entityManager = $em;
    }

    /**
     * @param array $idList
     * @param array $returnFields
     * @param bool $addCategory
     * @param bool $addMerchant
     * @param array $order
     *
     * @return array
     */
    public function getObjectArrayDataByIds(
        $idList, $returnFields = array(), $addCategory = false, $addMerchant = false, array $order = null
    )
    {
        if (empty($idList)) {
            return $idList;
        }

        $returnFields = array_merge($this->fields, $returnFields);
        $returnFields = array_unique($returnFields);
        $select       = array();
        foreach ($returnFields as $f) {
            $select[] = 'e.' . $f;
        }

        if ($addMerchant) {
            $select[] = 'm.title as merchantTitle, m.id as merchantId, m.slug as merchantSlug,'
                . ' m.logo as merchantLogo, m.description as merchantDescription';
        }

        if ($addCategory) {
            $select[] = 'c.title as categoryTitle, c.id as categoryId, c.slug as categorySlug';
        }

        $select = implode(',', $select);
        $entity = $this->getEntity();
        $qb     = $this->getEntityManager()->createQueryBuilder();
        $query  = $qb->select($select)
            ->setParameter(':id', $idList)
            ->from($entity, 'e');

        if ($addMerchant) {
            $query->innerJoin('e.merchant', 'm');
        }

        if ($addCategory) {
            $query->innerJoin('e.category', 'c');
        }

        if (is_array($idList)) {
            $query->where($qb->expr()->in('e.id', ':id'));
        } else {
            $query->setMaxResults(1);
            $query->where($qb->expr()->eq('e.id', ':id'));
        }

        if ($order) {
            $query->orderBy('e.' . key($order), current($order));
        }

        $query->setEnableHydrationCache($this->enableResultCache);
        $query = $query->getQuery();
        if ($addCategory) {
            $query = $this->addHints($query);
        }

        if (is_array($idList)) {
            return $query->getArrayResult();
        }

        return $query->getOneOrNullResult();
    }

    /**
     * Find object by id in repository
     *
     * @param int @id id of an object
     *
     * @return \SynergyCommon\Entity\AbstractEntity
     */
    public function findObject($id = 0)
    {
        return $this->findOneBy(array('id' => $id));
    }

    /**
     * @param array $params
     * @param array $params
     * @param int $mode
     *
     * @return mixed|null
     */
    public function findOneBy(array $params, $mode = AbstractQuery::HYDRATE_OBJECT)
    {
        try {
            $query = $this->getFindByQueryBuilder($params);
            $query->setMaxResults(1);

            return $query->getQuery()->getOneOrNullResult($mode);
        } catch (\Exception $exception) {
            $this->getLogger()->err($exception->getMessage());

            return null;
        }
    }

    /**
     * @param array $param
     * @param QueryBuilder $queryBuilder
     * @param null $alias
     *
     * @return QueryBuilder
     */
    protected function getFindByQueryBuilder(array $param, QueryBuilder $queryBuilder = null, $alias = null)
    {
        $alias        = $alias ?: $this->getAlias();
        $queryBuilder = $queryBuilder ?: $this->getEntityManager()->createQueryBuilder();
        $query        = $queryBuilder->select($alias)->from($this->getEntity(), $alias);
        $count        = 0;
        foreach ($param as $key => $value) {
            if (is_null($value) or $value == 'null') {
                $query->andWhere(
                    $queryBuilder->expr()->isNull($alias . '.' . $key)
                );
            } elseif (is_array($value)) {
                if (count($value)) {
                    $query->andWhere(
                        $queryBuilder->expr()->in($alias . '.' . $key, $value)
                    );
                }
            } else {
                $placeHolder = ':' . $key . ++$count;
                $query->andWhere(
                    $queryBuilder->expr()->eq($alias . '.' . $key, $placeHolder)
                );
                $query->setParameter($placeHolder, $value);
            }
        }

        return $query;
    }

    /**
     * @param      $params
     * @param      $limit
     * @param int $mode
     * @param bool $paginate
     * @param int $page
     *
     * @return array|null|\Zend\Paginator\Paginator
     */
    public function findItemsByCriteria(
        $params,
        $limit = null,
        $mode = AbstractQuery::HYDRATE_OBJECT,
        $paginate = false,
        $page = 1
    )
    {
        $query = $this->getFindByQueryBuilder($params);
        try {
            $query->setMaxResults($limit);

            if ($paginate) {

                $adapter   = new DoctrinePaginator($query);
                $paginator = new \Zend\Paginator\Paginator($adapter);
                $paginator->setCurrentPageNumber($page);
                $paginator->setItemCountPerPage($limit);

                return $paginator;
            }

            return $query->getQuery()->getResult($mode);
        } catch (\Exception $exception) {
            $this->getLogger()->err($query->getDQL());
            $this->getLogger()->err($exception->getMessage());

            return null;
        }
    }

    /**
     * @param array $param
     * @param QueryBuilder $queryBuilder
     * @param int $mode
     *
     * @return mixed|null
     */
    public function findOneTranslatedBy(
        array $param, QueryBuilder $queryBuilder = null, $mode = AbstractQuery::HYDRATE_OBJECT
    )
    {
        $query = $this->getFindByQueryBuilder($param, $queryBuilder);
        try {
            $query->setMaxResults(1);
            $query->setEnableHydrationCache($this->enableResultCache);
            $query = LocaleAwareTrait::addHints($query->getQuery());

            return $query->getOneOrNullResult($mode);
        } catch (\Exception $exception) {
            $this->getLogger()->err($query->getDQL());
            $this->getLogger()->err($exception->getMessage());

            return null;
        }
    }

    public function fetchOne()
    {
        $query = $this->createQuery();

        return $query->getSingleResult();
    }

    /**
     * Remove record by id
     *
     * @param $id
     *
     * @return bool
     * @throws \SynergyCommon\Exception\InvalidArgumentException
     */
    public function remove($id)
    {
        $object = $this->findObject($id);
        if ($object) {
            $this->getEntityManager()->remove($object);
            $this->getEntityManager()->flush();
            $retv = true;
        } else {
            throw new InvalidArgumentException(
                sprintf('Entity with ID #%d was not found', $id)
            );
        }

        return $retv;
    }

    /**
     * Save given entity
     *
     * @param $entity
     *
     * @return mixed
     * @throws \Exception
     */
    public function save($entity)
    {
        try {
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        } catch (\Exception $exception) {
            $this->getLogger()->logException($exception);

            return false;
        }

        return $entity;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->_entityManager->getRepository($this->getEntity());
    }

    public function listItemsByTitle()
    {

        $list = array();
        $qb   = $this->_entityManager->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e')
            ->from($this->_entity, 'e')
            ->getQuery();

        $result = $query->execute(array(), AbstractQuery::HYDRATE_ARRAY);

        if ($result) {
            foreach ($result as $item) {
                $list[$item['id']] = array(
                    'title'       => $item['title'],
                    'description' => isset($item['description']) ? $item['description'] : $item['title']
                );
            }
        }

        return $list;
    }

    public static function getUniqueGridIdentifier(array $options)
    {
        return implode('_', array_filter($options));
    }

    public function __call($method, $args)
    {
        $repo = $this->getRepository($this->getEntity());
        try {
            return Util::customCall($repo, $method, $args);
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to execute method {$method} - " . $e->getMessage());
        }
    }

    public function getEntityIdBySlug($data)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e.id')
            ->from($this->_entity, 'e')
            ->where('e.slug = :slug')
            ->setParameter(':slug', $data)
            ->setMaxResults(1)
            ->getQuery();

        try {
            $id = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $id = null;
        }

        return $id;
    }

    /**
     * @param $data
     *
     * @return mixed|null
     */
    public function getEntityLikeSlug($data)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e.id')
            ->from($this->_entity, 'e')
            ->where('e.slug LIKE :slug')
            ->setParameter(':slug', "{$data}%")
            ->setMaxResults(1)
            ->getQuery();

        try {
            $id = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $id = null;
        }

        return $id;
    }

    /**
     * @param array $idList
     * @param array $order
     * @param int $mode
     *
     * @return array
     */
    public function getItemListByIds(array $idList, array $order = null, $mode = AbstractQuery::HYDRATE_OBJECT)
    {
        if (empty($idList)) {
            return $idList;
        }
        $entity = $this->getEntity();
        $qb     = $this->getEntityManager()->createQueryBuilder();

        $query = $qb
            ->select('e')
            ->from($entity, 'e')
            ->where($qb->expr()->in('e.id', $idList));

        if ($order) {
            $query->orderBy('e.' . key($order), current($order));
        }

        return $query->getQuery()->getResult($mode);
    }

    /**
     * Returns a subset of fields based on the criteria
     *
     * @param       $idField
     * @param       $idValue
     * @param array $returnFields
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getFieldsByQueryBuilder($idField, $idValue, array $returnFields)
    {
        $select = array();
        $alias  = $this->getAlias();
        foreach ($returnFields as $f) {
            $select[] = $alias . '.' . $f;
        }

        $query = $this->_entityManager
            ->createQueryBuilder()
            ->select(implode(',', $select))
            ->from($this->_entity, $alias)
            ->where("e.$idField = :val")
            ->setParameter(":val", $idValue);

        return $query;
    }

    /**
     * Returns a subset of fields based on the criteria
     *
     * @param       $idField
     * @param       $idValue
     * @param array $returnFields
     * @param null $limit
     * @param int $mode
     *
     * @return array|mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFieldsBy(
        $idField, $idValue, array $returnFields, $limit = null, $mode = AbstractQuery::HYDRATE_OBJECT
    )
    {
        $query = $this->getFieldsByQueryBuilder($idField, $idValue, $returnFields);

        if ($limit == 1) {
            try {
                return $query->getQuery()->getSingleResult($mode);
            } catch (NoResultException $e) {
                return null;
            }
        } elseif ($limit) {
            $query->getMaxResults($limit);

            return $query->getQuery()->getArrayResult();
        }

        return array();
    }

    /**
     * Update and foreign entity
     *
     * @param $id
     * @param $param
     * @param $value
     *
     * @return \Doctrine\ORM\Mapping\Entity
     */
    public function updateForeignEntity($id, $param, $value)
    {
        $entity  = $this->getRepository($this->_entity)->find($id);
        $mapping = $this->getEntityManager()->getClassMetadata($this->_entity);
        $target  = $mapping->associationMappings[$param]['targetEntity'];

        /** @var $collection ArrayCollection */
        $collection = $entity->$param;
        if ($collection) {
            $collection->clear();
        } else {
            $collection = new ArrayCollection();
        }
        $value = array_unique(array_filter($value));

        foreach ($value as $v) {
            if ($foreignEntity = $this->getEntityManager()->find($target, $v)) {
                $collection->add($foreignEntity);
            }
        }

        $entity->$param = $collection;

        return $this->save($entity);
    }

    /**
     * @param $id
     * @param $field
     * @param $value
     *
     * @return mixed
     */
    public function updateById($id, $field, $value)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $updQuery     = $queryBuilder
            ->update($this->getEntity(), 'e')
            ->set("e.{$field}", $queryBuilder->expr()->literal($value))
            ->where('e.id = :id')
            ->setParameters(
                array(':id' => $id)
            )
            ->getQuery();

        return $updQuery->execute();
    }

    /**
     * Update and entity with data in params
     *
     * @param       $entityId
     * @param array $params
     *
     * @return array
     */
    public function updateEntity($entityId, array $params)
    {
        $error   = false;
        $message = '';
        $entity  = $this->getRepository($this->getEntity())->find($entityId);
        $mapping = $this->getEntityManager()->getClassMetadata($this->_entity);

        try {

            foreach ($params as $param => $value) {
                if (array_key_exists($param, $mapping->fieldMappings)
                    or array_key_exists($param, $mapping->associationMappings)
                ) {
                    $method = 'set' . ucfirst($param);
                    if (isset($mapping->fieldMappings[$param])) {
                        $type = $mapping->fieldMappings[$param]['type'];
                        if ($type == 'datetime' || $type == 'date') {
                            $value = $value ? new \DateTime($value) : null;
                        }
                        $entity->$method($value);
                    } elseif (isset($mapping->associationMappings[$param])) {
                        $target = $mapping->associationMappings[$param]['targetEntity'];

                        /** @var $collection ArrayCollection */
                        $collection = $entity->$param;
                        if ($mapping->associationMappings[$param]['type'] == 8) {
                            $collection->clear();
                            $value = explode(',', $value);
                            $value = array_unique(array_filter($value));

                            foreach ($value as $v) {
                                if ($foreignEntity = $this->getEntityManager()->find($target, $v)) {
                                    $collection->add($foreignEntity);
                                }
                            }
                            $entity->$param = $collection;
                        } else {
                            if (is_numeric($value)) {
                                $foreignEntity = $this->getEntityManager()->find($target, $value);
                            } else {
                                //assumes we are finding by slug
                                $foreignEntity = $this->getEntityManager()
                                    ->getRepository($target)
                                    ->findOneBy(array('slug' => $value));
                            }

                            $entity->$method($foreignEntity);
                        }
                    }
                }
            }

            if (!$error) {
                return $this->save($entity);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error   = true;
        }

        return array(
            $error,
            $message
        );
    }

    public function getOrm()
    {
        return $this->orm;
    }

    /**
     * Get paginatored list
     *
     * @return Paginator
     */
    public function getPaginator()
    {
        $query     = $this->createQuery();
        $paginator = new DoctrinePaginator($query);

        return $paginator;
    }

    /**
     * Create Query
     *
     * @return \Doctrine\Orm\Query
     */
    public function createQuery()
    {
        $mapping = $this->getEntityManager()->getClassMetadata($this->getEntity());

        $alias  = $this->getAlias();
        $offset = ($this->getOptions()->getPage() - 1) * $this->getOptions()->getPerPage();

        $this->_qb  = $this->getEntityManager()->createQueryBuilder();
        $toJoin     = array();
        $toSelect[] = $alias;
        $num        = ord($alias);
        $fields     = $this->getOptions()->getFields();
        $entityKey  = $this->getEntityKey();

        if (isset($fields[$entityKey])) {
            foreach ($fields[$entityKey] as $field) {
                if (isset($mapping->associationMappings[$field])) {
                    $joinAlias      = chr(++$num);
                    $toJoin[$field] = $joinAlias;
                    $toSelect[]     = $joinAlias;
                }
            }
        }

        $this->_qb->select(implode(',', $toSelect));
        $this->_qb->from($this->getEntity(), $alias);

        foreach ($toJoin as $field => $joinAlias) {
            $this->_qb->innerJoin($alias . '.' . $field, $joinAlias);
        }

        if ($itemCountPerPage = $this->getOptions()->getPerPage()) {
            $this->_qb->setMaxResults($itemCountPerPage);
        }

        if ($offset) {
            $this->_qb->setFirstResult($offset);
        }

        if ($filter = $this->getOptions()->getFilters()) {
            $this->_filter($filter);
        }

        $sortOrder = $this->getOptions()->getSortOrder();
        if (is_array($sortOrder)) {
            /** @var $sort \SynergyCommon\Model\Config\SortOrder */
            foreach ($sortOrder as $sort) {
                $this->_qb->addOrderBy($alias . '.' . $sort->getField(), $sort->getDirection());
            }
        }

        $query = $this->_qb->getQuery()->setHydrationMode(
            $this->getOptions()->getHydrationMode()
        );

        return $query;
    }

    /**
     * Filter the result set based on criteria.
     *
     * @param array $options
     *
     * @throws InvalidArgumentException
     */
    protected function _filter($options = array())
    {
        if ($options) {
            $count       = 0;
            $entityClass = $this->getEntity();
            /** @var $entity \SynergyCommon\Entity\AbstractEntity */
            $entity = new $entityClass();

            $inputFilter = $entity->getInputFilter() ?: new InputFilter();

            $mapping = $this->getEntityManager()->getClassMetadata($entityClass);

            foreach ($options as $field => $param) {
                if (is_array($param)) {
                    $value      = $param;
                    $expression = self::DEFAULT_EXPRESSION;
                } else {
                    list($value, $expression) = explode(':', $param . ':');
                }
                $expression = $expression ? trim(strtolower($expression)) : self::DEFAULT_EXPRESSION;

                if (array_key_exists($field, $mapping->fieldMappings)
                    or array_key_exists($field, $mapping->associationMappings)
                ) {
                    $type = $mapping->fieldMappings[$field]['type'];

                    if (isset($this->_operator[$expression])) {
                        $operator = $this->_operator[$expression];
                    } else {
                        $operator = $this->_operator[self::DEFAULT_EXPRESSION];
                    }
                    //filter inputs, process if valid or no filter was set
                    if ($inputFilter->has($field)) {
                        $input = $inputFilter->get($field);
                        $input->setValue($value);

                        if ($input->isValid()) {
                            $value = $input->getValue();
                        } else {
                            throw new InvalidArgumentException($field . ': ' . implode(' ', $input->getMessages()));
                        }
                    }

                    if ($type == 'boolean' and !(is_numeric($value) or is_bool($value))) {
                        $value = ('true' === $value) ? 1 : 0;
                    } elseif (is_string($value) and strpos($value, ',') !== false) {
                        $value = array_filter(explode(',', $value));
                    }

                    if (is_array($value)) {
                        if ($expression == self::NOT_IN) {
                            $this->_qb->andWhere(
                                $this->_qb->expr()->notIn(
                                    sprintf('%s.%s', $this->getAlias(), $field),
                                    $value
                                )
                            );
                        } else {
                            $this->_qb->andWhere(
                                $this->_qb->expr()->in(
                                    sprintf('%s.%s', $this->getAlias(), $field),
                                    $value
                                )
                            );
                        }
                    } else {
                        $placeHolder = ':' . $field . '_' . $count++;
                        $replacement = str_replace('?', $placeHolder, $operator);
                        $where       = sprintf('%s.%s %s', $this->getAlias(), $field, $replacement);

                        $this->_qb->andWhere($where);
                        $this->_qb->setParameter($placeHolder, $this->_setWildCardInValue($expression, $value));
                    }
                }
            }
        }
    }

    /**
     * @param                $entity
     * @param                $params
     *
     * @return \SynergyCommon\Entity\AbstractEntity
     * @throws  InvalidArgumentException
     */
    public function populateEntity($entity, $params)
    {
        if ($entity instanceof AbstractEntity) {

            $mapping = $this->getEntityManager()->getClassMetadata($this->getEntity());

            foreach ($params as $param => $value) {
                if (array_key_exists($param, $mapping->fieldMappings) or array_key_exists(
                        $param, $mapping->associationMappings
                    )
                ) {

                    $method = 'set' . ucfirst($param);
                    $value  = ($value == 'null' or (empty($value) and !is_numeric($value))) ? null : $value;

                    if (isset($mapping->associationMappings[$param])) {
                        $target = $mapping->associationMappings[$param]['targetEntity'];

                        if ($mapping->associationMappings[$param]['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                            throw new InvalidArgumentException(
                                sprintf(
                                    "OneToMany updates not supported: %s was not updated", $param
                                )
                            );
                        } elseif ($mapping->associationMappings[$param]['type'] == ClassMetadataInfo::MANY_TO_MANY
                        ) {
                            /** @var \Doctrine\Orm\PersistentCollection $param */
                            if ($entity->$param) {
                                $entity->$param->clear();
                            } else {
                                $entity->$param = new ArrayCollection();
                            }
                            $value = is_string($value) ? explode(',', $value) : $value;
                            $value = array_unique(array_filter($value));

                            foreach ($value as $v) {
                                if ($foreignEntity = $this->getEntityManager()->find($target, $v)) {
                                    $entity->$param->add($foreignEntity);
                                } else {
                                    throw new InvalidArgumentException(
                                        sprintf(
                                            "%s with ID #%d was not found  ", $param, $v
                                        )
                                    );
                                }
                            }
                        } elseif ($value) {
                            $value = is_array($value) ? current($value) : $value;
                            if ($foreignEntity = $this->getEntityManager()->find($target, $value)) {
                                $entity->$method($foreignEntity);
                            } else {
                                throw new InvalidArgumentException(
                                    sprintf(
                                        "%s with ID #%d was not found ", $param, $value
                                    )
                                );
                            }
                        }
                    } else {
                        $type = $mapping->fieldMappings[$param]['type'];
                        if ($type == 'datetime' || $type == 'date') {
                            try {
                                //attempt to ensure date is in acceptable format for datetime object
                                $ts    = is_numeric($value) ? $value : strtotime($value);
                                $ds    = $ts ? date(\DateTime::ISO8601, $ts) : null;
                                $value = $ds ? new \DateTime($ds) : null;
                                $entity->$method($value);
                            } catch (\Exception $e) {
                                throw new InvalidArgumentException(
                                    sprintf("%s: Wrong date format for column ", $param)
                                );
                                break;
                            }
                        } else {
                            $entity->$method($value);
                        }
                    }
                }
            }
        } else {
            throw new InvalidArgumentException('Invalid entity found');
        }

        return $entity;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function arrayToSearchIndexFormat(array $data)
    {
        $item = array();
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value      = trim($value);
                $value      = preg_replace('/[\p{C}]/iu', ' ', $value);
                $item[$key] = \html_entity_decode($value, null, 'UTF-8');
            }
        }

        $result = array_filter(
            $item,
            function ($value) {
                return (\is_bool($value) or \is_numeric($value) or !empty($value));
            }
        );

        return $result;
    }

    /**
     * Place wildcard filtering in value
     *
     * @param string $expression expression to filter
     * @param string $value value to add wildcard to
     *
     * @return string
     */
    protected function _setWildCardInValue($expression, $value)
    {
        switch ($expression) {
            case self::BEGIN_WITH:
            case self::NOT_BEGIN_WITH:
                $value = $value . '%';
                break;
            case self::END_WITH:
            case self::NOT_END_WITH:
                $value = '%' . $value;
                break;
            case self::CONTAIN:
            case self::NOT_CONTAIN:
                $value = '%' . $value . '%';
                break;
            case self::IN:
            case self::NOT_IN:
                $value = '(' . implode(', ', (array)$value) . ')';
                break;
        }

        return $value;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->_alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * @param \SynergyCommon\Model\Config\ModelOptions $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * @return \SynergyCommon\Model\Config\ModelOptions
     */
    public function getOptions()
    {
        if (!$this->_options) {
            $this->_options = new ModelOptions();
        }

        return $this->_options;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function setQb($qb)
    {
        $this->_qb = $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQb()
    {
        return $this->_qb;
    }

    /**
     * @param string $entityKey
     */
    public function setEntityKey($entityKey)
    {
        $this->_entityKey = $entityKey;
    }

    /**
     * @return string
     */
    public function getEntityKey()
    {
        return $this->_entityKey;
    }

    public function filterSearchData($data)
    {

        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = \html_entity_decode(trim($v), null, 'UTF-8');
            }
        }

        $result = array_filter(
            $data, function ($v) {
            return (\is_bool($v) or \is_numeric($v) or !empty($v));
        }
        );

        return $result;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Determines if the date is expired
     *
     * @param $end
     *
     * @return bool
     */
    public static function isExpired($end)
    {
        if ($end) {
            try {
                $now     = new \DateTime();
                $endDate = ($end instanceof \DateTime) ? $end : new \DateTime($end);

                return ($now > $endDate);
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Valid data to be imported
     *
     * @param array $data
     * @param array $columns
     * @param bool $escape
     *
     * @return array
     */
    public function validateRecordToImport(array $data, array $columns, $escape = true)
    {
        $class = $this->getEntity();
        /** @var $entity \SynergyCommon\Entity\AbstractEntity */
        $entity  = new $class();
        $filters = $entity->getInputFilter();

        foreach ($data as $field => $value) {

            $attribute = isset($columns[$field]) ? $columns[$field] : null;

            if ($filters and $attribute and $filters->has($attribute)) {
                $input = $filters->get($attribute);
                $input->setValue($value);
                if ($input->isValid()) {
                    $value = $input->getValue();
                } else {
                    $value = null;
                }
            }

            if ($value instanceof AbstractEntity) {
                $value = $value->getId();
            } elseif ($value instanceof \DateTime) {
                $value = $value->format(self::DB_DATE_FORMAT);
            } elseif (is_bool($value)) {
                $value = $value ? 1 : 0;
            }

            $data[$field] = $escape ? (get_magic_quotes_gpc() ? $value : addslashes($value)) : $value;
        }

        return $data;
    }

    /**
     * Creates a new node as child of a parent node or as a root node
     *
     * @param       $parentId
     * @param array $data
     *
     * @return \SynergyCommon\Entity\AbstractEntity|mixed
     */
    public function createNode($parentId, $data = array())
    {
        $className = $this->getEntity();
        /** @var $node \SynergyCommon\Entity\AbstractEntity */
        $node = new $className;
        $node = $node->exchangeArray($data);

        /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repo = $this->getRepository();

        if ($parentId and $parentNode = $this->findObject($parentId)) {
            $repo->persistAsFirstChildOf($node, $parentNode);
        } else {
            $repo->persistAsLastChild($node);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        return $node;
    }

    /**
     * Move a node relatived to the reference node in the direction specified
     * Direction can be: after, before, last or first
     *
     * @param $id
     * @param $referenceNodeId
     * @param $direction
     *
     * @return mixed
     * @throws \SynergyCommon\Exception\InvalidArgumentException
     * @throws \SynergyCommon\Exception\InvalidEntityException
     */
    public function moveNode($id, $referenceNodeId, $direction)
    {
        /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repo = $this->getRepository();

        if (!$node = $this->findObject($id)) {
            throw new InvalidEntityException(sprintf('Object with ID #%d was not ofund: ', $id));
        }

        if (!$referenceNode = $this->findObject($referenceNodeId)) {
            throw new InvalidEntityException(sprintf('Object with ID #%d was not ofund: ', $referenceNodeId));
        }

        switch ($direction) {
            case 'after':
                $done = $repo->persistAsNextSiblingOf($node, $referenceNode);
                break;
            case 'before':
                $done = $repo->persistAsPrevSiblingOf($node, $referenceNode);
                break;
            case 'last':
                $done = $repo->persistAsLastChildOf($node, $referenceNode);
                break;
            case 'first':
                $done = $repo->persistAsFirstChildOf($node, $referenceNode);
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid direction {%s) found. Direction should be either after, before, first or last.',
                        $direction
                    )
                );
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        return $done ? $node : $done;
    }

    /**
     * Delete a node from the nested set tree
     *
     * @param $id
     *
     * @return mixed
     */
    public function removeNode($id)
    {
        /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repo = $this->getRepository();
        $node = $this->findObject($id);
        $repo->removeFromTree($node);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        return true;
    }

    /**
     * @param  $acl
     */
    public function setAcl($acl)
    {
        $this->_acl = $acl;
    }

    /**
     * @return
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * @param object $identity
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;
    }

    /**
     * @return object
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    protected function disableSiteFilter()
    {
        (new Container())->offsetSet(self::FILTER_SESSION_KEY, true);
    }
}
