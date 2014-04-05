<?php
namespace SynergyCommon\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Zend\Navigation\Navigation;

class NestedSetRepository
    extends NestedTreeRepository
{

    /**
     * Returns the navigation menus
     *
     * @param int $rootLevel
     *
     * @return mixed
     */
    public function getTreeStructure($rootLevel = 1)
    {
        $entityManager = $this->getEntityManager();
        $meta          = $this->getClassMetadata();
        $config        = $this->listener->getConfiguration($this->_em, $meta->name);

        $query = $entityManager
            ->createQueryBuilder()
            ->select('e')
            ->from($config['useObjectClass'], 'e')
            ->where('e.level > :level')
            ->setParameter(':level', $rootLevel - 1)
            ->orderBy('e.root, e.lft', 'ASC')
            ->getQuery();

        return $this->_addHints($query)->getArrayResult();
    }

    public function getRootMenu()
    {
        $meta   = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $qb = $this->getEntityManager()->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */

        $query = $qb->select('e')
            ->from($config['useObjectClass'], 'e')
            ->where($qb->expr()->eq('e.level', ':level'))
            ->setMaxResults(1)
            ->setParameter('level', 0)
            ->getQuery();

        return $this->_addHints($query)->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function getRootMenuById($pageId)
    {
        $meta   = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $qb = $this->_em->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */

        $query = $qb->select('e')
            ->from($config['useObjectClass'], 'e')
            ->where($qb->expr()->eq('e.id', ':id'))
            ->setMaxResults(1)
            ->setParameter('id', $pageId)
            ->getQuery();

        return $this->_addHints($query)->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

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

    public function getNavigationContainer($menus, $routeMatch)
    {
        $nestedMenus = $this->toHierarchy($menus, $routeMatch);

        return new Navigation($nestedMenus);
    }

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
                    $stack[]   = & $trees[$i];
                } else {
                    // Add node to parent
                    $i                            = count($stack[$l - 1][$childKey]);
                    $stack[$l - 1][$childKey][$i] = $item;
                    $stack[]                      = & $stack[$l - 1][$childKey][$i];
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
        $meta   = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $path   = array();

        try {
            $qb = $this->_em->createQueryBuilder();
            $query
                = $qb->select('e')
                ->from($config['useObjectClass'], 'e')
                ->where('e.slug = :slug')
                ->setParameter(':slug', $slug)
                ->setMaxResults(1)
                ->getQuery();

            $menu = $this->_addHints($query)->execute(array(), AbstractQuery::HYDRATE_OBJECT);

            if ($menu) {
                $path = $this->getPath($menu[0]);
            }
        } catch (NonUniqueResultException $ex) {

        } catch (QueryException $ex) {

        }

        return $path;
    }

    /**
     * Add hints to the query
     *
     * @param AbstractQuery $query
     *
     * @return AbstractQuery
     */
    protected function _addHints(AbstractQuery $query)
    {
        return $query;
    }
}