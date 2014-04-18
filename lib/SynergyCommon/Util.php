<?php
namespace SynergyCommon;

class Util
{

    /**
     * Ensure string is returned
     *
     * @param $value
     *
     * @return array|string
     */
    public static function ensureIsString($value)
    {
        if (is_object($value)) {
            return $value;
        } elseif (is_array($value)) {
            return implode(',', array_filter($value));
        } else {
            return (string)$value;
        }
    }
}