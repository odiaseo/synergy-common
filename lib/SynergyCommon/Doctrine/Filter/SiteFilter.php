<?php

namespace SynergyCommon\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class SiteFilter extends SQLFilter implements ServiceManagerAwareInterface
{
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_servicemanager;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {

        if (isset($targetEntity->associationMappings['siteId'])
            and $targetEntity->associationMappings['siteId']['type'] != ClassMetadataInfo::MANY_TO_MANY
        ) {
            try {
                /** @var $site \SynergyCommon\Entity\Site */
                $site = $this->_servicemanager->get('active_site');

                return $targetTableAlias . '.site_id = ' . $site->getId();
            } catch (\Exception $e) {
                $site = null; //@todo logg error
                return '';
            }

        } else {
            return '';
        }
    }

    function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_servicemanager = $serviceManager;

        return $this;
    }
}