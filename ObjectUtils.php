<?php
class ObjectUtils {
	public static function replaceAttributes($objectToReturn, $objectToFind) {
		foreach ( $objectToReturn as $key => $val ) {
			if (property_exists ( $objectToFind, $key )) {
				$objectToReturn->$key = $objectToFind->$key;
			}
		}
		return $objectToReturn;
	}
	public static function deleteObjects($arrayObject, $arrayObjectToExclude, $strObjectKey) {
		foreach ( $arrayObject as $k => $object ) {
			foreach ( $arrayObjectToExclude as $objectSearch ) {
				if ($object->$strObjectKey == $objectSearch->$strObjectKey)
					unset ( $arrayObject [$k] );
			}
		}
		return $arrayObject;
	}
}