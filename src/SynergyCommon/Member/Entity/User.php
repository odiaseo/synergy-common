<?php
namespace SynergyCommon\Member\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseUser;
use ZfcUser\Entity\UserInterface;

/**
 * User
 *
 * @ORM\Entity
 * @ORM\Table(name="User")
 *
 */
class User
    extends BaseUser
    implements UserInterface, ProviderInterface
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="SynergyCommon\Member\Entity\Role")
     * @ORM\JoinTable(name="User_Role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;
    /**
     * @ORM\ManyToMany(targetEntity="SynergyCommon\Member\Entity\UserGroup")
     * @ORM\JoinTable(name="User_Group")
     */
    private $userGroups;

    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles      = new ArrayCollection();
        $this->userGroups = new ArrayCollection();
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

    public function setUserGroups($userGroups)
    {
        $this->userGroups = $userGroups;
    }

    public function getUserGroups()
    {
        return $this->userGroups;
    }

}