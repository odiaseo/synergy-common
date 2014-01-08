<?php
namespace SynergyCommon\Admin\Entity;
use Gedmo\Mapping\Annotation as Gedmo,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\BasePage;


/**
 * AdminMenu
 *
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @ORM\Table(name="Admin_Menu")
 * @Gedmo\Tree(type="nested")
 *
 */
class AdminMenu extends BasePage
{
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="AdminMenu", inversedBy="children", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;
    /**
     * @ORM\OneToMany(targetEntity="AdminMenu", mappedBy="parent", fetch="LAZY")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function setParent(AdminMenu $parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }
}