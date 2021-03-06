<?php
namespace SynergyCommon\ModelTrait;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Translatable\TranslatableListener;
use SynergyCommon\Doctrine\CachedEntityManager;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\Model\NestedSetRepositoryTrait;
use SynergyCommon\Util;
use Laminas\Session\Container;

/**
 * Class LocateAwareTrait
 * @method getEntity()
 * @method CachedEntityManager | EntityManager getEntityManager()
 * @method NestedSetRepositoryTrait getRepository()
 */
trait LocaleAwareTrait
{
    /** @var string */
    protected static $_locale = null;

    /** @var string */
    protected static $namespace = 'synergy';

    /**
     * @param       $locale
     * @param null $limit
     * @param array $ids
     * @param $field
     *
     * @return mixed
     */
    public function getItemsToTranslate($locale, $limit = null, array $ids = null, $field = '')
    {
        $query = $this->getItemsToTranslateQueryBuilder($locale, $limit, $ids, $field);

        return $query->getQuery()->useResultCache(false)->execute();
    }

    /**
     * @param       $locale
     * @param null $limit
     * @param array $ids
     * @param $field
     *
     * @return QueryBuilder
     */
    protected function getItemsToTranslateQueryBuilder($locale, $limit = null, array $ids = null, $field = '')
    {
        $params = array(
            ':locale' => $locale
        );

        $qb    = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->select('e')
            ->from($this->getEntity(), 'e');

        if ($field) {
            $query->leftJoin(
                'e.translations', 't', 'WITH',
                $query->expr()->andX(
                    $query->expr()->eq('t.locale', ':locale'),
                    $query->expr()->eq('t.field', ':field')
                )
            );

            $params[':field'] = $field;
        } else {
            $query->leftJoin(
                'e.translations', 't', 'WITH',
                $qb->expr()->eq('t.locale', ':locale')
            );
        }
        $query->andWhere($qb->expr()->isNull('t.object'));
        if ($limit) {
            $query->setMaxResults($limit);
        }

        if ($ids) {
            $query->andWhere($qb->expr()->in('e.id', $ids));
        }

        $query->setParameters($params);

        return $query;
    }

    /**
     * @param       $locale
     * @param       $entity
     *
     * @return QueryBuilder
     */
    public function getTranslationsByLocale($entity, $locale)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $query   = $builder
            ->select('e, t')
            ->from($entity, 'e')
            ->innerJoin(
                'e.translations', 't', 'WITH',
                $builder->expr()->eq('t.locale', ':locale')
            )->setParameters(array(':locale' => $locale));

        return $query->getQuery()->execute();
    }

    public static function addHints(AbstractQuery $query)
    {
        $language = self::getCurrentLocale();

        if ($language != Util::DEFAULT_LOCALE) {
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language)
                ->setHint(TranslatableListener::HINT_FALLBACK, 1)
                ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\Translatable\Query\TreeWalker\TranslationWalker');
        }

        return $query;
    }

    /**
     * Get active locale from session
     *
     * @return mixed|string
     */
    protected static function getCurrentLocale()
    {
        $container = new Container(self::getNamespace());
        if ($container->offsetExists(AbstractModel::SESSION_LOCALE_KEY)) {
            $locale = $container->offsetGet(AbstractModel::SESSION_LOCALE_KEY);
        } elseif (self::$_locale) {
            $locale = static::$_locale;
        } else {
            $locale = Util::DEFAULT_LOCALE;
        }

        return $locale;
    }

    public function setLocale($locale)
    {
        self::$_locale = $locale;
    }

    /**
     * @return string
     */
    public static function getNamespace()
    {
        return self::$namespace;
    }

    /**
     * @param string $namespace
     */
    public static function setNamespace($namespace)
    {
        self::$namespace = $namespace;
    }
}
