<?php
namespace SynergyCommon\Service;

use Doctrine\Common\Cache\MemcacheCache;
use SynergyCommon\SiteAwareInterface;

/**
 * Class MemcacheService
 *
 * Ensures cache is unique by site
 *
 * @package SynergyCommon\Service
 */
class   MemcacheService
    extends MemcacheCache
    implements SiteAwareInterface
{

    /** @var \SynergyCommon\Entity\AbstractEntity */
    protected $_site;

    public function setSite($site)
    {
        $this->_site = $site;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return parent::doFetch($this->_getHashedSiteId($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return parent::doContains($this->_getHashedSiteId($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return parent::doSave($this->_getHashedSiteId($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return parent::doDelete($this->_getHashedSiteId($id));
    }

    /**
     * Get unique ID for the site by suffixing with the site ID
     *
     * @param $id
     *
     * @return string
     */
    protected function _getHashedSiteId($id)
    {
        return $id . (int)$this->_site->getId();
    }
}