<?php
namespace SynergyCommon\Image;

interface ImageConverterInterface
{

    /**
     * Covert file to type format
     *
     * @param        $filename
     * @param        $newfilename
     * @param array $dimensions
     * @param string $type
     *
     * @return mixed
     */
    public function convert($filename, $newfilename, $dimensions = array(), $type = 'jpg');
}