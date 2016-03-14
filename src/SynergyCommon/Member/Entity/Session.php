<?php
namespace SynergyCommon\Member\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * Session
 *
 * @ORM\Entity
 * @ORM\Table(name="Session")
 *
 */
class Session extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=40, name="session_id");
     */
    protected $sessionId;
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=25)
     */
    protected $name;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $data;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $modified;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lifetime;
    /**
     * @ORM\Column(type="integer", nullable=true, name="expire_by")
     */
    protected $expireBy;

    /**
     * @return mixed
     */
    public function getExpireBy()
    {
        return $this->expireBy;
    }

    /**
     * @param mixed $expireBy
     */
    public function setExpireBy($expireBy)
    {
        $this->expireBy = $expireBy;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
