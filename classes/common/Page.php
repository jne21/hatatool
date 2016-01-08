<?php

namespace common;

use common\Registry;
use common\Template;

class Page {

	const
		TABLE = 'page',
		DB    = 'db',
		FORCE_HIDDEN = TRUE,

		MODE_POPUP  = 1,
		MODE_NORMAL = 0;

	public
		$content,
		$breadcrumbs,
		$h1,
		$mode,
		$title,
		$metaTags,
		$headers = [];

	function __construct($url=NULL, $forceHidden=FALSE, $alternativeUrl = '404') {

		if ($url) {

			$registry = Registry::getInstance();
			$db = $registry->get(self::DB);
				
			$rsp = $db->query(
"SELECT * FROM `".self::TABLE."` WHERE `url`=".$db->escape($url).($forceHidden ? '': ' AND `show`<>0')
			);

			if ($sap = $db->fetch($rsp)) {
				$this->headers = array (
					'HTTP/1.1 200 OK' => TRUE,
					'Status: 200 OK'  => TRUE
				);

				$this->content             = $sap['html'];
				$this->breadcrumbs         = $sap['breadcrumbs'];
				$this->h1                  = $sap['h1'];
				$this->mode                = $sap['mode'];
				$this->title               = $sap['title'];
				$this->metaTags            = $sap['meta'];
			}
			else {
				$this->headers = array (
					'HTTP/1.1 404 Not found' => TRUE,
					'Status: 404 Not found'  => TRUE
				);

				$rsp = $db->query("SELECT * FROM `".self::TABLE."` WHERE `url`=".$db->escape($alternativeUrl));
				if ($sap = $db->fetch($rsp)) {
					$this->content             = $sap['html'];
					$this->breadcrumbs         = $sap['breadcrumbs'];
					$this->h1                  = $sap['title'];
					$this->mode                = $sap['mode'];
					$this->title               = $sap['meta_title'];
					$this->metaTags            = $sap['meta'];

					$this->renderProperty('content_main', array ('original_url' => $url));
				}
				else {
					$this->content = 'Error: Page not found.';
				}
			}
		}
	}

	function renderProperty($propertyName, $data) {
		$tpl = new Template($this->$propertyName);
		$this->$propertyName = $tpl->apply($data);
		return $this;
	}

	/**
	 * Standard setter
	 * @param string $propertyName
	 * @param mixed $value
	 * @return object
	 */
	function set($propertyName, $value) {
	    $this->$propertyName = $value;
	    return $this;
	}

	/**
	 * Standard getter
	 * @param string $propertyName
	 * @return mixed
	 */
	function get($propertyName) {
	    return $this->$propertyName;
	}
	
}
