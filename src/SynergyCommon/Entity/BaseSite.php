<?php

namespace SynergyCommon\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 */
class BaseSite extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    /**
     * @ORM\Column(type="string")
     */
    protected $domain;
    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive;
    /**
     * @ORM\Column(type="boolean", name="is_subdomain")
     */
    protected $isSubDomain = 0;
    /**
     * @ORM\Column(type="integer", name="offer_count", options={"default"=0})
     */
    protected $offerCount = 0;
    /**
     * @ORM\Column(type="integer", name="voucher_count", options={"default"=0})
     */
    protected $voucherCount = 0;
    /**
     * @ORM\Column(type="string", length=15)
     */
    protected $locale = 'en_US';
    /**
     * @ORM\Column(type="string", length=15, name="i18n_locale", nullable=true)
     */
    protected $i18nLocale;
    /**
     * @var \datetime createdAt
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;
    /**
     * @var \Datetime updatedAt
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updatedAt;

    /**
     * @return mixed
     */
    public function getOfferCount()
    {
        return $this->offerCount;
    }

    /**
     * @param mixed $offerCount
     */
    public function setOfferCount($offerCount)
    {
        $this->offerCount = $offerCount;
    }

    /**
     * @return mixed
     */
    public function getVoucherCount()
    {
        return $this->voucherCount;
    }

    /**
     * @param mixed $voucherCount
     */
    public function setVoucherCount($voucherCount)
    {
        $this->voucherCount = $voucherCount;
    }

    /**
     * @return mixed
     */
    public function getI18nLocale()
    {
        return $this->i18nLocale;
    }

    /**
     * @param mixed $i18nLocale
     */
    public function setI18nLocale($i18nLocale)
    {
        $this->i18nLocale = $i18nLocale;
    }

    /**
     * @return mixed
     */
    public function getIsSubDomain()
    {
        return $this->isSubDomain;
    }

    /**
     * @param mixed $isSubDomain
     */
    public function setIsSubDomain($isSubDomain)
    {
        $this->isSubDomain = $isSubDomain;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param \datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getDomain()
    {
        return $this->domain;
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

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
