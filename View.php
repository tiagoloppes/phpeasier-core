<?php
class View {
	private static $data = array ();
	public function __construct() {
	}
	public function dispatch($pageName = 'index') {
		header ( 'Content-Type: text/html; charset=' . CHARSET );
		$the_controller = strtolower ( preg_replace ( '/^(.+)Controller$/', '$1', THE_CONTROLLER ) );
		if (file_exists ( VIEW_PATH . $the_controller . '/' . $pageName . '.view.php' )) {
			require VIEW_PATH . $the_controller . '/' . $pageName . '.view.php';
		} else {
			desenv_error ( 'View não encontrada: ' . VIEW_PATH . $the_controller . '/' . $pageName . '.view.php' );
		}
	}
	public function message($error_message) {
		header ( 'Content-Type: text/html; charset=' . CHARSET );
		require TEMPLATE_PATH . '/errors/_404.php';
	}
	public static function assign($key, $val) {
		self::$data [$key] = $val;
	}
	public static function setData($key, $val) {
		self::$data [$key] = $val;
	}
	public static function getData($key, $defaultData = NULL) {
		return isset ( self::$data [$key] ) ? self::$data [$key] : $defaultData;
	}
	public static function getRecaptacha() {
		return recaptcha_get_html ( RECAPTCHA_PUBLIC_KEY );
	}
}
