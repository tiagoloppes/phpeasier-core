<?php
/* Incluindo classes base */
require_once SYSTEM_PATH . 'Controller.php';
require_once SYSTEM_PATH . 'View.php';
require_once SYSTEM_PATH . 'Model.php';
require_once SYSTEM_PATH . 'Request.php';
require_once SYSTEM_PATH . 'Session.php';
require_once SYSTEM_PATH . 'Text.php';
require_once SYSTEM_PATH . 'Decimal.php';
require_once SYSTEM_PATH . 'Image.php';
require_once SYSTEM_PATH . 'Date.php';
require_once SYSTEM_PATH . 'Between.php';
require_once SYSTEM_PATH . 'FormUtils.php';
require_once SYSTEM_PATH . 'ObjectUtils.php';
require_once SYSTEM_PATH . 'FileUpload.php';
require_once SYSTEM_PATH . 'SQLUtils.php';

Session::initialize ();

/* Incluindo funções auxiliares */
require_once SYSTEM_PATH . 'functions.php';

/* Verificando se existe arquivo de funções do app */
if (file_exists ( APP_PATH . 'functions.php' )) {
	require_once APP_PATH . 'functions.php';
}

/* Dabase connection */
require SYSTEM_PATH . 'database/DBConnector.php';
class Engine {
	private static $the_controller;
	private static $the_action;
	private static $the_value;
	private static $the_value2;
	private static $the_value3;
	private static $requestAction;
	public static function initialize($requestAction) {
		self::$requestAction = $requestAction;
		
		// the_controller
		self::$the_controller = str_replace ( ' ', '', ucwords ( str_replace ( array (
				'_',
				'-' 
		), array (
				' ',
				' ' 
		), preg_replace ( '/([^?]+)\??.*/', '$1', self::$requestAction [0] ) ) ) );
		
		if (isset ( self::$the_controller ) && self::$the_controller != NULL) {
			self::$the_controller = self::$the_controller . 'Controller';
		} else {
			self::$the_controller = DEFAULT_CONTROLLER;
		}
		
		// the_action
		self::$the_action = NULL;
		// the_value
		self::$the_value = NULL;
		// the_value 2
		self::$the_value2 = NULL;
		// the_value 3
		self::$the_value3 = NULL;
		
		if (isset ( self::$requestAction [1] )) {
			self::$the_action = lcfirst ( str_replace ( ' ', '', ucwords ( str_replace ( array (
					'_',
					'-' 
			), array (
					' ',
					' ' 
			), self::$requestAction [1] ) ) ) );
		}
		self::$the_action = self::$the_action != NULL ? self::$the_action : 'index';
		
		if (isset ( self::$requestAction [2] )) {
			self::$the_value = self::$requestAction [2];
		}
		if (isset ( self::$requestAction [3] )) {
			self::$the_value2 = self::$requestAction [3];
		}
		if (isset ( self::$requestAction [4] )) {
			self::$the_value3 = self::$requestAction [4];
		}
	}
	public static function run() {
		/* Captura o Request */
		if (count ( $_REQUEST ) > 0) {
			foreach ( $_REQUEST as $key => $val ) {
				Request::set ( $key, $val );
			}
		}
		
		/* Importa o controller ou trata o erro se não existir controller */
		if (self::$the_controller == NULL) {
			self::$the_controller = DEFAULT_CONTROLLER;
		}
		
		if (file_exists ( CONTROLLER_PATH . self::$the_controller . '.php' )) {
			require CONTROLLER_PATH . self::$the_controller . '.php';
			define ( 'THE_CONTROLLER', self::$the_controller );
			self::$the_controller = new self::$the_controller ();
		} else {
			self::$the_controller = ERROR_CONTROLLER;
			require CONTROLLER_PATH . self::$the_controller . '.php';
			define ( 'THE_CONTROLLER', self::$the_controller );
			self::$the_controller = new self::$the_controller ();
		}
		
		/* Captura a query string */
		$base = str_replace ( '/', '\/', BASE_PATH );
		$the_query_string = preg_replace ( '/^' . $base . '(.*)/', '$1', $_SERVER ['REQUEST_URI'] );
		
		/* Invoca o action */
		if (method_exists ( self::$the_controller, self::$the_action ) || self::$the_action == 'index') {
			$ctr = self::$the_controller;
			$atc = self::$the_action;
			$ctr->$atc ( self::$the_value, self::$the_value2, self::$the_value3, $the_query_string );
		} elseif (is_object ( self::$the_controller ) && ! method_exists ( self::$the_controller, self::$the_action )) {
			self::$the_value = self::$requestAction [1];
			self::$the_value2 = self::$requestAction [2];
			self::$the_value3 = self::$requestAction [3];
			self::$the_controller->index ( self::$the_value, self::$the_value2, self::$the_value3, $the_query_string );
		} else {
			self::$the_controller->error404 ();
			exit ();
		}
	}
}