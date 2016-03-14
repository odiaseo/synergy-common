<?php
namespace SynergyCommon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class BaseUser extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    protected $title = '';
    /**
     * @ORM\Column(type="string", length=64, name="first_name", nullable=true)
     */
    protected $firstName;
    /**
     * @ORM\Column(type="string", length=64, name="last_name", nullable=true)
     */
    protected $lastName;
    /**
     * @ORM\Column(type="string", length=64, name="username", nullable=true)
     */
    protected $username;
    /**
     * @ORM\Column(type="string", length=64, name="display_name", nullable=true)
     */
    protected $displayName;
    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    protected $password;
    /**
     * @ORM\Column(type="string", length=128, name="activation_code", nullable=true)
     */
    protected $activationCode;
    /**
     * @ORM\Column(type="date", name="dob", nullable=true)
     */
    protected $dateOfBirth;
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $telephone = '';
    /**
     * @ORM\Column(type="string", length=6)
     */
    protected $gender = 'male';
    /**
     * @ORM\Column(type="string", length=64, unique=true, nullable=false)
     */
    protected $email;
    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive = 0;
    /**
     * @ORM\Column(type="boolean", name="is_admin")
     */
    protected $isAdmin = 0;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $visits = 0;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $state;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $remarks = '';
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $avatar = '';

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

    public function setDisplayName($displayMame)
    {
        $this->displayName = $displayMame;
    }

    public function getDisplayName()
    {
        return $this->displayName;
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

    public function getSessionId()
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
