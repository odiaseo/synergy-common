<?php

namespace SynergyCommon\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Zend\ServiceManager\ServiceManager;

class SiteFilter
    extends SQLFilter
{

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
                return $targetTableAlias . '.site_id = ' . $this->getSite()->getId();
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

    public function setSite($site)
    {
        $this->_site = $site;
        return $this ;
    }

    public function getSite()
    {
        return $this->_site;
    }

    public function setLogger($logger)
    {
        $this->_logger = $logger;
        return $this ;
    }

    public function getLogger()
    {
        return $this->_logger;
    }

}