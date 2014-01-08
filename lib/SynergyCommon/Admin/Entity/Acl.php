<?php
namespace SynergyCommon\Admin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity;


/**
 * Acl
 *
 * @ORM\Entity
 * @ORM\Table(name="Acl")
 *
 */
class Acl
    extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=50, name="controller");
     */
    private $controller;
    /**
     * @ORM\Column(type="string", length=50, name="action", nullable=true);
     */
    private $action;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="SynergyCommon\Member\Entity\Role", cascade="all")
     * @ORM\JoinTable(name="Acl_Role",
     *      joinColumns={@ORM\JoinColumn(name="acl_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
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
     * @param \Doctrine\Common\Collections\Collection $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

}