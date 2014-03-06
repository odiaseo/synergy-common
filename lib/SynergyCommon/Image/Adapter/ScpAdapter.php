<?php
namespace SynergyCommon\Image\Adapter;

use SynergyCommon\Image\TransferAdapterInterface;

class ScpAdapter
    implements TransferAdapterInterface
{
    /** @var \SynergyCommon\Image\Config\ImageCompressorOptions */
    public $_options;

    /**
     * @param \SynergyCommon\Image\Config\ImageCompressorOptions $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * @return \SynergyCommon\Image\Config\ImageCompressorOptions
     */
    public function getOptions()
    {
        return $this->_options;
    }


    public function copy($filename, $destination)
    {
        $password = $this->_options->getRemotePassword();
        $password = $password ? ':' . $password : '';

        $command = sprintf(
            'scp -p %s %s%s@%s:%s',
            $filename,
            $this->_options->getRemoteUser(),
            $password,
            $this->_options->getRemoteHost(),
            $destination
        );

        return \shell_exec($command) ? false : true;
    }
}