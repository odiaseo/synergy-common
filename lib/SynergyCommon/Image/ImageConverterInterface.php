<?php
namespace SynergyCommon\Image;

interface ImageConverterInterface
{

    /**
     * Covert file to type format
     *
     * @param        $filename
     * @param string $type
     *
     * @return string
     */
    public function convert($filename, $type = 'jpg');

}