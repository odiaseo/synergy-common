<?php
namespace SynergyCommon\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\BaseEntity;

/**
 * @ORM\MappedSuperclass
 */
class BaseUser
    extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=15)
     */
    private $title = '';
    /**
     * @ORM\Column(type="string", length=64, name="first_name")
     */
    private $firstName;
    /**
     * @ORM\Column(type="string", length=64, name="last_name")
     */
    private $lastName;
    /**
     * @ORM\Column(type="string", length=64, name="username")
     */
    private $username;
    /**
     * @ORM\Column(type="string", length=64, name="display_name")
     */
    private $displayMame;
    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=128, name="activation_code", nullable=true)
     */
    private $activationCode;
    /**
     * @ORM\Column(type="date", name="dob")
     */
    private $dateOfBirth;
    /**
     * @ORM\Column(type="string", length=20)
     */
    private $telephone = '';
    /**
     * @ORM\Column(type="string", length=6)
     */
    private $gender;
    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $email;
    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    private $isActive = 0;
    /**
     * @ORM\Column(type="boolean", name="is_admin")
     */
    private $isAdmin = 0;
    /**
     * @ORM\Column(type="integer")
     */
    private $visits = 0;
    /**
     * @ORM\Column(type="integer")
     */
    private $state;
    /**
     * @ORM\Column(type="string")
     */
    private $remarks = '';
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $avatar = '';

    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;
    }

    public function getActivationCode()
    {
        return $this->activationCode;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setDisplayMame($displayMame)
    {
        $this->displayMame = $displayMame;
    }

    public function getDisplayMame()
    {
        return $this->displayMame;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    public function getRemarks()
    {
        return $this->remarks;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setVisits($visits)
    {
        $this->visits = $visits;
    }

    public function getVisits()
    {
        return $this->visits;
    }


}