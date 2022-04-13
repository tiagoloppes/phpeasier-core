<?php
class Router {
	private static $url;
	private static $basePath = BASE_PATH;
	private static $routes = array ();
	public static function addRoute($from, $to) {
		self::$routes [] = array (
				self::$basePath . preg_replace ( '/(.+)\/$/', '$1', $from ),
				self::$basePath . preg_replace ( '/(.+)\/$/', '$1', $to ) 
		);
	}
	public static function initialize() {
		self::$url = preg_replace ( '/(.+[^\/])$/', '$1/', preg_replace ( '/([^?]+)(\?.+)?/', '$1', $_SERVER ['REQUEST_URI'] ) );
	}
	/* Interpreta o roteador para urls estáticas, se não, para urls curingas */
	public static function getRoute() {
		if (count ( self::$routes ) > 0) {
			foreach ( self::$routes as $route ) {
				$route [0] = preg_replace ( '/([^*]+[^\/*])$/', '$1/', $route [0] );
				if ($route [0] == self::$url) {
					self::$url = $route [1];
					break;
				} elseif (preg_match ( '/(.+)\*$/', $route [0], $matches )) { // verifica se tem curinga para buscar (*)
					$pattern = '/^(' . str_replace ( '/', '\/', $matches [1] ) . ')(.*)$/';
					if (preg_match ( $pattern, self::$url, $strToFind )) {
						$route [1] = str_replace ( '$1', $strToFind [2], $route [1] );
						self::$url = $route [1];
						break;
					}
				}
			}
		}
		self::$url = preg_replace ( '/(.+)\/$/', '$1', self::$url );
		return explode ( '/', preg_replace ( '/^' . str_replace ( '/', '\/', self::$basePath ) . '(.*)$/', '$1', self::$url ) );
	}
	public static function redir($from, $to) {
		$from = preg_replace ( '/(.+[^\/])$/', '$1/', $from );
		$url = preg_replace ( '/(.+[^\/])$/', '$1/', self::$url );
		if ($url == self::$basePath . $from) {
			header ( 'location:' . self::$basePath . $to );
			exit ();
		}
	}
	public static function getSelfUrl() {
		return self::$url;
	}
}