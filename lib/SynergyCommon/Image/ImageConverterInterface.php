<?php
namespace SynergyCommon\Image;

interface ImageConverterInterface
{

    /**
     * Covert file to type format
     *
     * @param        $filename
     * @param array  $dimensions
     * @param string $type
     *
     * @return mixed
     */
    public function convert($filename, $dimensions = array(), $type = 'jpg');

}