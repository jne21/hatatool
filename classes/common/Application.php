<?php

namespace common;

use \common\Redirect;
use \common\RedirectQuery;
use \common\Registry;
use \common\Module;

class Application {

	public
		$originalUrl,
		$parsedUrl,
		$redirectSourceUrl,
		$redirectSourceRequest,
		$segment = [],
		$protocol
	;

	private function __construct()
	{
		if (
			isset($_SERVER['HTTPS'])
			&& (
				$_SERVER['HTTPS'] == 'on'
				|| $_SERVER['HTTPS'] == 1
			)
			|| $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
		) {
			$this->protocol = 'https://';
		} else {
			$this->protocol = 'http://';
		}
		$this->siteRoot = $this->protocol . $_SERVER['HTTP_HOST'] . '/';
		$this->originalUrl = self::getOriginalUrl();
		$this->parsedUrl = parse_url($site_protocol . $_SERVER['HTTP_HOST'] . $this->originalUrl);
		$this->redirectSourceUrl = substr($this->parsedUrl['path'], 1);
		$this->redirectSourceRequest = $this->redirectSourceUrl;
		if ($_SERVER["REDIRECT_QUERY_STRING"]) {
			$this->redirectSourceRequest .= '?'.$_SERVER["REDIRECT_QUERY_STRING"];
		}
		$this->segment = explode('/', $this->redirectSourceUrl);
	}

	private function __clone()
	{}

	public static function getInstance()
	{
		static $me;
		return is_object($me) ? $me : $me = new self();
	}

	public function route()
	{
		$registry = Registry::getInstance();
		foreach (Redirect::getList(Redirect::ACTIVE) as $redirect) {
			if (preg_match($redirect->source, $this->redirectSourceUrl)) {
				Redirect::updateRequestDate($redirect->id);
				RedirectQuery::register();
				$destination = str_replace('{site_root}', $registry->get('site_root'), $redirect->destination);
				header('Location: '.$destination, TRUE, $registry->status);
				die();
			}
		}
		foreach (Module::getList() as $module) {
			if (preg_match($module->url, substr($this->originalUrl, 1))) {
				$controllerName = $module->className . 'Controller';
				$controller = new $controllerName;
				exit;
			}
		}
	}

	public static function getOriginalUrl ()
	{
		// Apache
		if (strpos($_SERVER['SERVER_SOFTWARE'],'Apache') === 0) {
			switch(php_sapi_name()) {
				case "cgi":
					$result = !empty($_ENV['REQUEST_URI']) ? urldecode($_ENV['REQUEST_URI']): null;
					break;
				case "cgi-fcgi":
					@$result = ($_SERVER['REDIRECT_URL'] != PATH_RELATIVE.'index.php') ? urldecode($_SERVER['REDIRECT_URL']) : urldecode($_ENV['REQUEST_URI']);
					break;
				default:
					$result = empty($_SERVER['REDIRECT_URL']) ? null : urldecode($_SERVER['REDIRECT_URL']);
					break;
			}
		// Lighty
		} elseif (strpos($_SERVER['SERVER_SOFTWARE'],'lighttpd') === 0) {
			if (isset($_SERVER['ORIG_PATH_INFO'])) {
				$result = urldecode($_SERVER['ORIG_PATH_INFO']);
			} elseif (isset($_SERVER['REDIRECT_URI'])){
				$result = urldecode(substr($_SERVER['REDIRECT_URI'],9));
			}
		}
		return $result;
	}
}
