<?php

namespace CMS;

use \common\Registry;

class Page {

	const
		TABLE = 'page',
		DB    = 'db',
		FORCE_HIDDEN = TRUE,

		MODE_POPUP  = 1,
		MODE_NORMAL = 0;

	public
		$headers = [];

	function __construct($url=NULL, $forceHidden=FALSE, $alternativePage = '404') {
		$registry = Registry::getInstance();
		$db = $registry->get(self::DB);

		if ($url) {

			$rsp = $db->query(
"SELECT * FROM `".self::TABLE."` WHERE `url`=".$db->escape($url).($forceHidden ? '': ' AND `show`<>0')
			);

//$this->db->escape(implode('/', $segment))
//die ("[".$db->escape(implode('/', $segment))."]");
			if ($sap = $db->fetch($rsp)) {
				$this->headers = array (
					'HTTP/1.1 200 OK' => TRUE,
					'Status: 200 OK'  => TRUE
				);

				$this->content             = $sap['html'];
				$this->breadcrumbs         = $sap['breadcrumbs'];
				$this->h1                  = $sap['title'];
				$this->pageMode            = $sap['popup'];
				$this->metaTitle           = $sap['meta_title'];
				$this->metaTags            = $sap['meta'];
				$this->has_left_column     = $sap['has_left_column'];
				$this->left                = $sap['html_left'];
			}
			else {
				$this->headers = array (
					'HTTP/1.1 404 Not found' => TRUE,
					'Status: 404 Not found'  => TRUE
				);

				$rsp = $db->query("SELECT * FROM `".self::TABLE."` WHERE `url`=".$db->escape($alternativePage)) or die('Page: '.$db->lastError);
				if ($sap = $db->fetch($rsp)) {
					$this->content             = $sap['html'];
					$this->breadcrumbs         = $sap['breadcrumbs'];
					$this->h1                  = $sap['title'];
					$this->pageMode            = $sap['popup'];
					$this->metaTitle          = $sap['meta_title'];
					$this->metaTags           = $sap['meta'];
					$this->has_left_column     = $sap['has_left_column'];
					$this->left                = $sap['html_left'];

					$this->renderProperty('content_main', array ('original_url' => $url));
				}
				else {
					$this->content = 'Error: Page not found.';
				}
			}
		}
	}

	function renderProperty($propertyName, $data) {
		$tpl = new Template($this->$propertyName, Template::SOURCE_VARIABLE);
		$this->$propertyName = $tpl->apply($data);
	}
}
