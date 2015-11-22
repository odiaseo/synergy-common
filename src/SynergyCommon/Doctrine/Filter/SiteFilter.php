<?php

namespace SynergyCommon\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class SiteFilter
 *
 * @package SynergyCommon\Doctrine\Filter
 */
class SiteFilter extends SQLFilter
{
    use ServiceLocatorAwareTrait;

    /**
     * @var \SynergyCommon\Entity\BaseSite
     */
    protected $site;

    /** @var \Zend\Log\Logger */
    protected $logger;

    /**
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (isset($targetEntity->associationMappings['site'])
            and $targetEntity->associationMappings['site']['type'] != ClassMetadataInfo::MANY_TO_MANY
        ) {
            try {
                return $this->getSiteFilterQuery($targetTableAlias, $targetEntity->getName());
            } catch (\Exception $e) {
                if ($this->getLogger()) {
                    $this->getLogger()->err($e->getMessage());
                }

                return '';
            }
        } else {
            return '';
        }
    }

    protected function _getSiteId()
    {
        $site = $this->getSite();
        if ($site instanceof \ArrayObject) {
            return $site->id;
        } else {
            return $site->getId();
        }
    }

    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get filter query
     *
     * @param $targetTableAlias
     * @param $targetEntity
     *
     * @return string
     */
    public function getSiteFilterQuery($targetTableAlias, $targetEntity)
    {
        if ($id = $this->getSite()->getId()) {
            return $targetTableAlias . '.site_id = ' . $id;
        }

        return '';
    }
}
