<?php
namespace SynergyCommon\ModelTrait;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\Model\NestedSetRepository;
use Zend\Session\Container;

/**
 * Class LocateAwareTrait
 *
 * @method getEntity()
 * @method \Doctrine\ORM\EntityManager getEntityManager()
 * @method NestedSetRepository getRepository()

 */
trait LocaleAwareTrait
{
    protected static $_locale = 'en_GB';

    public function getItemsToTranslate($locale, $limit = null)
    {

        $qb    = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->select('e')
            ->from($this->getEntity(), 'e')
            ->leftJoin(
                'e.translations', 't', 'WITH',
                $qb->expr()->eq('t.locale', ':locale')
            )->setParameters(array(':locale' => $locale))
            ->andWhere($qb->expr()->isNull('t.object'));

        if ($limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->useResultCache(false)->execute();
    }

    public static function addHints(AbstractQuery $query)
    {
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, self::getCurrentLocale())
            ->setHint(TranslatableListener::HINT_FALLBACK, 1)
            ->setHint(
                Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\Translatable\Query\TreeWalker\TranslationWalker'
            );

        return $query;
    }

    /**
     * Get active locale from session
     *
     * @return mixed|string
     */
    protected static function getCurrentLocale()
    {
        $container = new Container();
        if ($container->offsetExists(AbstractModel::SESSION_LOCALE_KEY)) {
            $locale = $container->offsetGet(AbstractModel::SESSION_LOCALE_KEY);
        } else {
            $locale = static::$_locale;
        }

        $localeData = \Locale::parseLocale($locale);

        return $localeData['language'];
    }

    /**
     * @param null $rootPage
     *
     * @return array
     */
    public function getEntityNavigation($rootPage = null)
    {
        /** @var $repo \SynergyCommon\Model\NestedSetRepository */
        $repo  = $this->getRepository();
        $query = $this->addHints($repo->getNodesHierarchyQuery($rootPage));

        return $query->getArrayResult();
    }

    public function setLocale($locale)
    {
        self::$_locale = $locale;
    }
}