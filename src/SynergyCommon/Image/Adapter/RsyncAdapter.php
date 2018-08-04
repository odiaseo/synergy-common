<?php

namespace SynergyCommon\Image\Adapter;

use SynergyCommon\Image\TransferAdapterInterface;
use SynergyCommon\Util;

/**
 * Class RsyncAdapter
 *
 * @package SynergyCommon\Image\Adapter
 */
class RsyncAdapter implements TransferAdapterInterface
{
    /** @var \SynergyCommon\Image\Config\ImageCompressorOptions */
    public $_options;

    /**
     * @return \SynergyCommon\Image\Config\ImageCompressorOptions
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param \SynergyCommon\Image\Config\ImageCompressorOptions $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function copy($filename, $destination)
    {
        $hostIp = Util::getLocalhostIp();

        if ($this->_options->getRemoteHost() == $hostIp) {
            $destFilename = rtrim($destination, '/') . '/' . basename($filename);

            if (copy($filename, $destFilename)) {
                return 'Local copy to ' . $destFilename;
            } else {
                return 'Local copy failed';
            }
        }

        $password = $this->_options->getRemotePassword();
        $password = $password ? ':' . $password : '';
        $command  = sprintf(
            'rsync -avi %s %s%s@%s:%s',
            $filename,
            $this->_options->getRemoteUser(),
            $password,
            $this->_options->getRemoteHost(),
            $destination
        );

        return \shell_exec($command);
    }
}
