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
     * @ORM\Column(type="string", length=32);
     */
    protected $id;
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=3)
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

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
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
