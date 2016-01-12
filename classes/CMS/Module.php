<?php
namespace CMS;
use \common\Registry;

class Module extends \common\SimpleObject {

	public
		$id   = NULL,
		$name = '',
		$url  = '',
		$path = '';

	const
        DB = 'db',
		TABLE = 'module',
		ORDER_FIELD_NAME = 'order'
    ;

	use \common\entity;
	
    function loadDataFromArray($data) {
	    $this->id    = $data['id'];
		$this->name  = $data['name'];
		$this->url   = $data['url'];
		$this->path  = $data['path'];
	}

	function save() {
		$db = Registry::getInstance()->get('db');
	    $record = array (
			'name'  => trim($this->name),
			'url'   => trim($this->url),
			'path'  => trim($this->path)
		);
		if ($this->id) {
			$db->update(self::TABLE, $record, "id=".$this->id);
		}
		else {
			$record[self::ORDER_FIELD_NAME] = self::getNextOrderIndex();
			$db->insert(self::TABLE, $record);
		}
	}

	static function getList($dumy = NULL) {
        return parent::getList("SELECT * FROM `".self::TABLE."` ORDER BY `".self::ORDER_FIELD_NAME."`");
	}

}
