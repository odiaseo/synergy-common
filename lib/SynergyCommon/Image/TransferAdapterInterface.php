<?php
namespace SynergyCommon\Image;

interface TransferAdapterInterface
{

    public function copy($filename, $destination);

    public function setOptions($options);
}