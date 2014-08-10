<?php
namespace SynergyCommon\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity;


/**
 * @ORM\MappedSuperclass
 */
class BasePage
    extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Translatable
     */
    protected $label;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description;
    /**
     * @ORM\Column(type="string", length=255, name="keywords", nullable=true)
     */
    protected $keywords;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $thumbnail;
    /**
     * @ORM\Column(name="is_visible",type="boolean")
     */
    protected $isVisible = 1;
    /**
     * @ORM\Column(name="is_adult",type="boolean")
     *
     */
    protected $isAdult = 0;
    /**
     * @ORM\Column(name="is_cached",type="boolean")
     */
    protected $isCached = 1;
    /**
     * @ORM\Column(name="route_name", type="string", nullable=false)
     */
    protected $routeName = 'application';
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $parameters;
    /**
     * @ORM\Column(type="string", name="icon_class_name")
     */
    protected $iconClassName = 'icon-th';
    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(name="slug", type="string")
     * @Gedmo\Translatable
     */
    protected $slug;
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    protected $lft;
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    protected $rgt;
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    protected $level;
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    protected $root;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $uri;
    /**
     * @var \datetime createdAt
     *
     * @ORM\Column(type="datetime", name="start_at", nullable=true)
     */
    protected $startAt;
    /**
     * @var \datetime createdAt
     *
     * @ORM\Column(type="datetime", name="end_at", nullable=true)
     */
    protected $endAt;


    public function __construct()
    {
        $this->children = new ArrayCollection();
    }


    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getChildren()
    {
        return $this->children;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \datetime $endAt
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @return \datetime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    public function setIconClassName($iconClassName)
    {
        $this->iconClassName = $iconClassName;
    }

    public function getIconClassName()
    {
        return $this->iconClassName;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setIsAdult($isAdult)
    {
        $this->isAdult = $isAdult;
    }

    public function getIsAdult()
    {
        return $this->isAdult;
    }

    public function setIsCached($isCached)
    {
        $this->isCached = $isCached;
    }

    public function getIsCached()
    {
        return $this->isCached;
    }

    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    public function getIsVisible()
    {
        return $this->isVisible;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    public function getLft()
    {
        return $this->lft;
    }

    public function setPageThemes($pageThemes)
    {
        $this->pageThemes = $pageThemes;
    }

    public function getPageThemes()
    {
        return $this->pageThemes;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }

    public function getRgt()
    {
        return $this->rgt;
    }

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
    }

    public function getRouteName()
    {
        return $this->routeName;
    }


    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param \datetime $startAt
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return \datetime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }


    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }


    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }


}