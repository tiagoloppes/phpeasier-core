<?php
class Session {
	private static $id;
	public static function initialize($id = NULL) {
		if ($id) {
			if (session_id ())
				session_destroy ();
			session_id ( $id );
			session_start ();
			self::$id = session_id ( $id );
		} elseif (! session_id ()) {
			session_start ();
			self::$id = session_id ();
		}
	}
	public static function set($key, $val) {
		$_SESSION [$key] = $val;
	}
	public static function get($key, $defaultData = NULL) {
		return isset ( $_SESSION [$key] ) ? $_SESSION [$key] : $defaultData;
	}
	public static function remove($key) {
		unset ( $_SESSION [$key] );
	}
	public static function getId() {
		return self::$id;
	}
}
