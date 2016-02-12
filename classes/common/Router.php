<?php

namespace common;

use \common\Registry;

class Router extends \common\SimpleObject {

	const
		DB = 'db',
		TABLE = 'router',
		ORDER_FIELD_NAME = 'order'
	;

	public
		$id,
		$name,
		$url,
		$controller
	;

	use \common\entity;

	static function getList($fake=NULL) {
		return parent::getList("SELECT * FROM `".self::TABLE."` ORDER BY `".self::ORDER_FIELD_NAME."`");
	}

	function loadDataFromArray($data) {
		$this->id    = $data['id'];
		$this->name  = $data['name'];
		$this->url   = $data['url'];
		$this->controller = $data['controller'];
	}

	function save() {
		$db = Registry::getInstance()->get('db');
		$record = array (
			'name'  => trim($this->name),
			'url'   => trim($this->url),
			'controller'  => trim($this->controller)
		);
		if ($this->id) {
			$db->update(self::TABLE, $record, "id=".$this->id);
		}
		else {
			$record[self::ORDER_FIELD_NAME] = self::getNextOrderIndex();
			$db->insert(self::TABLE, $record);
		}
	}

}
