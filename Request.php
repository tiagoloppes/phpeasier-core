<?php
class Request {
	private static $data = array ();
	public function __construct() {
	}
	public static function set($key, $val) {
		self::$data [$key] = $val;
	}
	public static function get($key, $defaultData = NULL) {
		if (is_array ( self::$data [$key] )) {
			return self::$data [$key];
		} elseif (isset ( self::$data [$key] ) && is_string ( self::$data [$key] )) {
			if (preg_match ( '/%/', self::$data [$key] )) {
				return urldecode ( self::$data [$key] );
			} elseif (preg_match ( '/&/', self::$data [$key] )) {
				return html_entity_decode ( self::$data [$key] );
			} else {
				return self::$data [$key];
			}
		} else {
			return $defaultData;
		}
	}
	public static function getInteger($key, $defaultData = 0) {
		return ( int ) self::get ( $key, $defaultData );
	}
	public static function getFloat($key, $defaultData = 0) {
		return ( float ) self::get ( $key, $defaultData );
	}
	public static function getFromJson($key) {
		return json_decode ( self::$data [$key] );
	}
	public static function getObjectSerialized($arrayForm) {
		$arrayForm = self::get ( $arrayForm );
		$newObject = new stdClass ();
		foreach ( $arrayForm as $element ) {
			$name = preg_replace ( '/[^a-zA-Z0-9_]/', '', $element ['name'] );
			if (is_array ( $newObject->{$name} )) {
				$newObject->{$name} [] = $element ['value'];
			} else if (key_exists ( $name, $newObject )) {
				$itemValue = $newObject->{$name};
				unset ( $newObject->{$name} );
				$newObject->{$name} = array (
						$itemValue,
						$element ['value'] 
				);
			} else {
				$newObject->{$name} = $element ['value'];
			}
		}
		return $newObject;
	}
	public static function getAllAsObject() {
		$obj = new stdClass ();
		foreach ( self::$data as $key => $item ) {
			$obj->$key = $item;
		}
		return $obj;
	}
	public static function sendPost($url, $arrayContent) {
		$postdata = http_build_query ( $arrayContent );
		$opts = array (
				'http' => array (
						'method' => 'POST',
						'header' => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata 
				) 
		);
		$context = stream_context_create ( $opts );
		return file_get_contents ( $url, false, $context );
	}
}
