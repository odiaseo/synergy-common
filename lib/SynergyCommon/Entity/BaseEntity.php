<?php
namespace SynergyCommon\Entity;

use SynergyCommon\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Session\Container;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity
    extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var bool
     */
    private $localized = false;
    /**
     * @ORM\Column(type="string")
     */
    private $timezone;
    /**
     * @var \DateTime createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
    /**
     * @var \DateTime updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    private $updatedAt;
    /**
     * @ORM\ManyToOne(targetEntity="SynergyCommon\Entity\Site", cascade="persist")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    public function __construct()
    {
        $date           = new \DateTime('now');
        $this->timezone = $date->getTimezone()->getName();
    }

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

    public function setSite($siteId)
    {
        $this->site = $siteId;
    }

    /**
     * @return \SynergyCommon\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
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
        return $this->timezone;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}