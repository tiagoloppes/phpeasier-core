<?php
class Controller {
	protected $view;
	public function __construct() {
		$this->view = new View ();
	}
	public function index() {
		$this->view->dispatch ( 'index' );
	}
	public function error404($msg = 'Página não encontrada.') {
		if (file_exists ( CONTROLLER_PATH . 'ErrorController.php' ) || 1 == 1) {
			$error = new ErrorController ( $msg );
			$error->error_404 ( $msg );
		} else {
			$this->view->message ( $msg );
		}
	}
	public static function formAction($action) {
		$urlController = preg_replace ( '/^(.+)Controller$/', '$1', get_called_class () );
		$urlController = preg_replace ( '/([A-Z]+)/', '_$1', $urlController );
		$urlController = preg_replace ( '/^_(.+)$/', '$1', $urlController );
		echo BASE_PATH . strtolower ( $urlController ) . '/' . $action;
	}
	public function redir($controller, $action = NULL, $value = NULL, $hashMode = false) {
		$action = ($action == 'index') ? '' : $action;
		$value = ($value != NULL && $action != NULL) ? '/' . $value : $value;
		$hash = $hashMode == true ? '#' : '';
		header ( 'location:' . BASE_PATH . $hash . $controller . '/' . $action . $value );
		exit ();
	}
	public function isValidRecaptcha() {
		$recap = recaptcha_check_answer ( RECAPTCHA_PRIVATE_KEY, $_SERVER ["REMOTE_ADDR"], $_POST ["recaptcha_challenge_field"], $_POST ["recaptcha_response_field"] );
		return $recap->is_valid;
	}
}
