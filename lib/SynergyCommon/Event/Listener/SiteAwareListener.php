<?php

namespace SynergyCommon\Event\Listener;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use SynergyCommon\Entity\BaseSite;
use SynergyCommon\Exception\Exception;

class SiteAwareListener
    implements EventSubscriber
{

    protected $_site;
    private $_field = 'site';

    /**
     * Specifies the list of events to listen
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate'
        );
    }


    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if ($this->isSiteAware($entity)) {
            $args->setNewValue($this->_field, $this->getSite());
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($this->isSiteAware($entity)) {
            $entity->setSiteId($this->getSite());
        }
    }

    protected function isSiteAware($entity)
    {
        $className = get_class($entity);
        $object    = new \ReflectionClass($className);
        $method    = 'get' . ucfirst($this->_field);

        return ($object->hasProperty($this->_field) and !$entity->{$method}());

    }

    public function setSite(BaseSite $site)
    {
        $this->_site = $site;

        return $this;
    }

    public function getSite()
    {
        if (!$this->_site instanceof BaseSite) {
            throw new Exception(basename(__CLASS__)
                . ": No valid site found. Ensure that the listener is initialised with a valid site.");
        }

        return $this->_site;
    }

    public function hasSite()
    {
        return $this->_site ? true : false;
    }

}