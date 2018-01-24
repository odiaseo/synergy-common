<?php
namespace SynergyCommon\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Util;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity extends AbstractEntity
{
    /**
     * @var bool
     */
    protected $localized = false;

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

    /**
     * @ORM\PrePersist
     */
    public function setCreatedDateTimeObject()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDateTimeObject()
    {
        if (empty($this->updatedAt)) {
            $this->updatedAt = new \DateTime();
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function ensureNoLineBreaks()
    {
        if (isset($this->description)) {
            $this->description = Util::removeLineBreaks($this->description);
        }

        if (isset($this->title)) {
           // $this->title = Util::removeLineBreaks($this->title);
        }
    }

    public function isFlexOfferLink($deepLink)
    {
        $links = [
            'http://track.flexlinks.com',
            'http://track.flexlinkspro.com',
        ];

        if (empty($deepLink)) {
            return false;
        }

        foreach ($links as $prefix) {
            if (stripos($deepLink, $prefix) !== false) {
                return true;
            }
        }

        return false;
    }
}
