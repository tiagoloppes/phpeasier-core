<?php
function get_template_part($templateName) {
	require 'app/template/' . $templateName . '.php';
}
function desenv_error($system_error_msg) {
	$path = preg_replace ( '/^(.+[^\/])$/', '$1/', SYSTEM_PATH );
	if (SHOW_DESENV_ERRORS) {
		require $path . 'pages/error_msg.php';
	}
}
function LoadController($controllerName) {
	require_once CONTROLLER_PATH . $controllerName . '.php';
}
function LoadModel($modelName) {
	require_once MODEL_PATH . $modelName . '.php';
}
function is_home() {
	if (BASE_PATH == $_SERVER ['REQUEST_URI'])
		return true;
	else
		return false;
}
function page_uri() {
	$pattern = '/^' . str_replace ( '/', '\/', BASE_PATH ) . '(.*)/';
	return preg_replace ( $pattern, '$1', $_SERVER ['REQUEST_URI'] );
}
function getGeoLocation() {
	if (! $_SESSION ['ipgeolocation']) {
		$ip = $_SERVER ['REMOTE_ADDR'];
		$url = 'http://api.ipstack.com/';
		$access_key = '2568d94642dd35dfc588e755a1ade044';
		$jsonstring = file_get_contents ( $url . $ip . '?access_key=' . $access_key );
		$result = json_decode ( $jsonstring );
		$geolocation = new stdClass ();
		$geolocation->ip = $ip;
		if ($result->country_code) {
			$geolocation->local = $result->country_code . $result->region_code;
			$geolocation->pais = $result->country_code;
			$geolocation->uf = $result->region_code;
			$geolocation->cidade = $result->city;
			$geolocation->navegador = $_SERVER ['HTTP_USER_AGENT'];
			$geolocation->pagina_entrada = $_SERVER ['REQUEST_URI'];
		} else {
			$geolocation->local = 'LOCALHOST';
		}
		$_SESSION ['ipgeolocation'] = $geolocation;
	}
	return $_SESSION ['ipgeolocation'];
}
function showAds() {
	if ($_GET ['ads'] || ! in_array ( getGeoLocation ()->local, array (
			'BRRS',
			'BRSC' 
	) ))
		return true;
	else
		return false;
}