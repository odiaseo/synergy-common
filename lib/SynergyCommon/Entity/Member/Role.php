<?php
namespace SynergyCommon\Entity\Member;

use BjyAuthorize\Acl\HierarchicalRoleInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity;

/**
 * Role
 *
 * @ORM\Entity
 * @ORM\Table(name="Role")
 *
 */
class Role
    extends BaseEntity
    implements HierarchicalRoleInterface
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;
    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", name="role_id", length=255, unique=true, nullable=true)
     */
    private $roleId;
    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;


    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \SynergyCommon\Entity\Member\Role $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \SynergyCommon\Entity\Member\Role
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    public function getRoleId()
    {
        return $this->roleId;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

}