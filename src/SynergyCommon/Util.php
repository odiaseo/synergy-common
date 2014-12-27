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

	/**
	 * @param $object
	 * @param $method
	 * @param $args
	 *
	 * @return mixed
	 */
	public static function customCall( $object, $method, $args ) {

		$numArgs = count( $args );

		switch ( $numArgs ) {
			case 0:
				return $object->$method();
			case 1:
				return $object->$method( $args[0] );
			case 2:
				return $object->$method( $args[0], $args[1] );
			case 3:
				return $object->$method( $args[0], $args[1], $args[3] );
			case 4:
				return $object->$method( $args[0], $args[1], $args[2], $args[3] );
			case 5:
				return $object->$method( $args[0], $args[1], $args[2], $args[3], $args[4] );
			case 6:
				return $object->$method( $args[0], $args[1], $args[2], $args[3], $args[4], $args[5] );
			case 7:
				return $object->$method( $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6] );
			default:
				return call_user_func_array( array( $object, $method ), $args );
		}
	}
}
