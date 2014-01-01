<?php
namespace SynergyCommon\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
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
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $title;
    /**
     * @ORM\Column(type="string")
     */
    private $label = '';
    /**
     * @ORM\Column(type="string", length=150)
     */
    private $description = '';
    /**
     * @ORM\Column(type="string", length=255, name="keywords")
     */
    private $keywords = '';
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $thumbnail = '';
    /**
     * @ORM\Column(name="is_visible",type="boolean")
     */
    private $isVisible = 1;
    /**
     * @ORM\Column(name="is_adult",type="boolean")
     *
     */
    private $isAdult = 0;
    /**
     * @ORM\Column(name="is_cached",type="boolean")
     */
    private $isCached = true;
    /**
     * @ORM\Column(name="route_name", type="string", nullable=false)
     */
    private $routeName = 'application';
    /**
     * @ORM\Column(type="string")
     */
    private $parameters = '';
    /**
     * @ORM\Column(type="string", name="icon_class_name")
     */
    private $iconClassName = 'icon-th';
    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(name="slug", type="string")
     */
    private $slug;
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $level;
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;
    /**
     * @ORM\Column(type="string")
     */
    private $uri = '';
    /**
     * @var \datetime createdAt
     *
     * @ORM\Column(type="datetime", name="start_at", nullable=true)
     */
    private $startAt;
    /**
     * @var \datetime createdAt
     *
     * @ORM\Column(type="datetime", name="end_at", nullable=true)
     */
    private $endAt;


    public function __construct()
    {
        $this->children   = new ArrayCollection();
        $this->pageThemes = new ArrayCollection();
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