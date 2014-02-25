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
            'scp -r %s%s@%s:%s',
            $this->_options->getRemoteUser(),
            $password,
            $this->_options->getRemoteHost(),
            $destination
        );

        \exec($command, $output, $return);

        return $return;
    }
}