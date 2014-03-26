<?php

namespace PageBuilder\Controller;

use Doctrine\ORM\EntityManager;
use SynergyCommon\SiteAwareInterface;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class BaseController
 *
 * @method \Zend\Http\Response getResponse()
 * @method \Zend\Http\Request  getRequest()
 * @method translate()
 * @package Application\Controller
 */
class BaseActionController
    extends AbstractActionController
    implements SiteAwareInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em = null;
    /** @var \PageBuilder\Entity\Site */
    protected $_site;
    /**@var $util \Application\Util\String */
    protected $_util;

    /** @var \Zend\Log\Logger */
    protected $_log;

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @return array|EntityManager|null|object
     */
    public function getEntityManager()
    {
        if (null === $this->_em) {
            // doctrine.entitymanager.orm_default
            $this->_em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->_em;
    }

    public function setSite($site = null)
    {
        $this->_site = $site;

        return $this;
    }

    public function getUtil()
    {
        if (!$this->_util) {
            $this->_util = $this->getServiceLocator()->get('util_string');
            $this->_util->setSite($this->_site);
        }

        return $this->_util;
    }

    /**
     * @return \Admin\Entity\Site
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * @param \Zend\Log\Logger $log
     *
     * @return $this
     */
    public function setLog($log)
    {
        $this->_log = $log;

        return $this;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getLog()
    {
        return $this->_log;
    }

}
