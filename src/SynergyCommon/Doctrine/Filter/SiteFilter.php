<?php

namespace SynergyCommon\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class SiteFilter extends SQLFilter
{
    use ServiceLocatorAwareTrait;

    /**
     * @var \SynergyCommon\Entity\BaseSite
     */
    protected $_site;

    /** @var \Zend\Log\Logger */
    protected $_logger;

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
        $this->_site = $site;

        return $this;
    }

    public function getSite()
    {
        return $this->_site;
    }

    public function setLogger($logger)
    {
        $this->_logger = $logger;

        return $this;
    }

    public function getLogger()
    {
        return $this->_logger;
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
