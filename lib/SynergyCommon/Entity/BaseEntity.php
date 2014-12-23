<?php
namespace SynergyCommon\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;
use Zend\Session\Container;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity
    extends AbstractEntity
{
    protected $_id;
    /**
     * @var bool
     */
    protected $localized = false;
    /**
     * @ORM\Column(type="string")
     */
    protected $timezone = 'UTC';
    /**
     * @var \DateTime createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;
    /**
     * @var \DateTime updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    protected $updatedAt;

    /**
     * @param $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $updateAt
     */
    public function setUpdatedAt($updateAt)
    {
        $this->updatedAt = $updateAt;
    }

    /**
     * @param boolean $localized
     */
    public function setLocalized($localized)
    {
        $this->localized = $localized;
    }

    /**
     * @return boolean
     */
    public function getLocalized()
    {
        return $this->localized;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getTimezone()
    {
        if (!$this->timezone) {
            $date           = new \DateTime('now');
            $this->timezone = $date->getTimezone()->getName();
        }

        return $this->timezone;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }
}