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
        if (is_object($value) or is_null($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return $value ? 1 : 0;
        } elseif (is_array($value)) {
            return implode(',', array_filter($value));
        } else {
            return (string)$value;
        }
    }
}