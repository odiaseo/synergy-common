<?php
namespace SynergyCommon\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use SynergyCommon\Doctrine\CacheAwareQueryTrait;
use SynergyCommon\ModelTrait\LocaleAwareTrait;
use Zend\Navigation\Navigation;

/**
 * Class NestedSetRepository
 *
 * @package SynergyCommon\Model
 */
trait NestedSetRepositoryTrait
{
    use CacheAwareQueryTrait;

    /**
     * Returns the navigation menus
     *
     * @param int $rootLevel
     *
     * @return mixed
     */
    public function getTreeStructure($rootLevel = 1)
    {
        /** @var NestedTreeRepository  | self | AbstractModel $this */
        $meta          = $this->getClassMetadata();
        $config        = $this->listener->getConfiguration($this->_em, $meta->name);
        $entityManager = $this->getEntityManager();
        $builder       = $entityManager->createQueryBuilder();

        $builder->select('e')
            ->from($config['useObjectClass'], 'e')
            ->where('e.level > :level')
            ->setParameter(':level', $rootLevel - 1)
            ->orderBy('e.root, e.lft', 'ASC');

        $query = $builder->getQuery();

        if ($this->isTranslatable($this)) {
            $query = LocaleAwareTrait::addHints($query);
        }

        return $query->getArrayResult();
    }

    /**
     * @return mixed
     */
    public function getRootMenu()
    {
        /** @var EntityRepository  | self | AbstractModel $this */
        /** @var $query \Doctrine\ORM\Query */
        /** @var NestedTreeRepository  | self | AbstractModel $this */
        $meta   = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $qb     = $this->getEntityManager()->createQueryBuilder();

        $query = $qb->select('e')
            ->from($config['useObjectClass'], 'e')
            ->where($qb->expr()->eq('e.level', ':level'))
            ->setMaxResults(1)
            ->setParameter('level', 0)
            ->getQuery();

        $query = $this->setCacheFlag($query);

        if ($this instanceof LocaleAwareTrait) {
            $query = LocaleAwareTrait::addHints($query);
        }

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @param $pageId
     * @return mixed
     */
    public function getRootMenuById($pageId)
    {
        /** @var $query \Doctrine\ORM\Query */
        /** @var NestedTreeRepository  | self | AbstractModel $this */
        $meta   = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $qb     = $this->_em->createQueryBuilder();

        $query = $qb->select('e')
            ->from($config['useObjectClass'], 'e')
            ->where($qb->expr()->eq('e.id', ':id'))
            ->setMaxResults(1)
            ->setParameter('id', $pageId)
            ->getQuery();

        $query = $this->setCacheFlag($query);

        if ($this->isTranslatable($this)) {
            $query = LocaleAwareTrait::addHints($query);
        }

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @param $arrs
     * @param string $depth_key
     * @return array
     */
    public function nestify($arrs, $depth_key = 'level')
    {
        $nested = array();
        $depths = array();

        foreach ($arrs as $key => $arr) {
            if ($arr[$depth_key] == 0) {
                $nested[$key]                 = $arr;
                $depths[$arr[$depth_key] + 1] = $key;
            } else {
                $parent =& $nested;
                for ($i = 1; $i <= ($arr[$depth_key]); $i++) {
                    $parent =& $parent[$depths[$i]];
                }

                $parent[$key]                 = $arr;
                $depths[$arr[$depth_key] + 1] = $key;
            }
        }

        return $nested;
    }

    /**
     * @param $menus
     * @param $routeMatch
     * @return Navigation
     */
    public function getNavigationContainer($menus, $routeMatch)
    {
        $nestedMenus = $this->toHierarchy($menus, $routeMatch);

        return new Navigation($nestedMenus);
    }

    /**
     * @param $collection
     * @param string $childKey
     * @param null $callback
     * @return array
     */
    public function toHierarchy($collection, $childKey = 'pages', $callback = null)
    {
        // Trees mapped

        $trees = array();
        //$l = 0;
        if (count($collection) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();
            foreach ($collection as $node) {
                if (is_callable($callback)) {
                    $item = call_user_func($callback, $node);
                } else {
                    $item = $node;
                }

                $item[$childKey] = array();
                // Number of stack items
                $l = count($stack);
                // Check if we're dealing with different levels
                while ($l > 0 && $stack[$l - 1]['level'] >= $item['level']) {
                    array_pop($stack);
                    $l--;
                }
                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root node
                    $i         = count($trees);
                    $trees[$i] = $item;
                    $stack[]   = &$trees[$i];
                } else {
                    // Add node to parent
                    $i                            = count($stack[$l - 1][$childKey]);
                    $stack[$l - 1][$childKey][$i] = $item;
                    $stack[]                      = &$stack[$l - 1][$childKey][$i];
                }
            }
        }

        return $trees;
    }

    /**
     * @param $slug
     *
     * @return array
     */
    public function getBreadcrumbPath($slug)
    {
        /** @var NestedTreeRepository  | self | AbstractModel $this */
        $path   = array();
        $meta   = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $qb     = $this->getEntityManager()->createQueryBuilder();

        $query = $qb->select('e')
            ->from($config['useObjectClass'], 'e')
            ->where('e.slug = :slug')
            ->setParameter(':slug', $slug)
            ->setMaxResults(1)
            ->getQuery();

        $query = $this->setCacheFlag($query);
        $menu  = $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);

        if ($menu) {

            $builder = $this->getPathQueryBuilder($menu);
            $builder->select('node.title, node.slug, node.level, node.parameters, node.routeName');

            $pathQuery = $builder->getQuery();
            $this->setEnableHydrationCache($this->enableResultCache);
            $pathQuery = $this->setCacheFlag($pathQuery);

            if ($this->isTranslatable($this)) {
                LocaleAwareTrait::addHints($query);
            }
            $path = $pathQuery->getArrayResult();
        }

        return $path;
    }

    /**
     * @param $class
     * @return bool
     */
    protected function isTranslatable($class)
    {
        $reflection = new \ReflectionClass($class);
        $traits     = $reflection->getTraitNames();

        return (in_array('SynergyCommon\ModelTrait\LocalAwareNestedSetTrait', $traits));
    }
}
