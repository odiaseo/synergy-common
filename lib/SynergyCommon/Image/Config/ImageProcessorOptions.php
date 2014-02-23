<?php
namespace SynergyCommon\Image\Config;

use Zend\Stdlib\AbstractOptions;

class ImageProcessorOptions
    extends AbstractOptions
{
    /** @var \SynergyCommon\Image\TransferAdapterInterface */
    protected $_adapter;

    protected $_directory;

    protected $_minQuality;

    protected $_maxQuality;

    protected $_remoteHost;

    protected $_remoteUser;

    protected $_remotePassword;

    public function setRemoteHost($remoteHost)
    {
        $this->_remoteHost = $remoteHost;
    }

    public function getRemoteHost()
    {
        return $this->_remoteHost;
    }

    public function setRemotePassword($remotePassword)
    {
        $this->_remotePassword = $remotePassword;
    }

    public function getRemotePassword()
    {
        return $this->_remotePassword;
    }

    public function setRemoteUser($remoteUser)
    {
        $this->_remoteUser = $remoteUser;
    }

    public function getRemoteUser()
    {
        return $this->_remoteUser;
    }

    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * @return \SynergyCommon\Image\TransferAdapterInterface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function setDirectory($directory)
    {
        $this->_directory = $directory;
    }

    public function getDirectory()
    {
        return $this->_directory;
    }

    public function setMaxQuality($maxQuality)
    {
        $this->_maxQuality = $maxQuality;
    }

    public function getMaxQuality()
    {
        return $this->_maxQuality;
    }

    public function setMinQuality($minQuality)
    {
        $this->_minQuality = $minQuality;
    }

    public function getMinQuality()
    {
        return $this->_minQuality;
    }


}