<?php
namespace CMS;
use \common\Registry;

class Module {

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
	
	function __construct($moduleId) {
		$db = Registry::getInstance()->get('db');
		if ($id = intval($moduleId)) {
			$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE id=$id");
			if ($sa = $db->fetch($rs)) {
				$this->id    = $id;
				$this->name  = $sa['name'];
				$this->url   = $sa['url'];
				$this->path  = $sa['path'];
			}
		}
	}

	function save() {
		$record = array (
			'name'  => trim($this->name),
			'url'   => trim($this->url),
			'path'  => trim($this->path)
		);
		if ($this->id) {
			$this->db->update(self::TABLE, $record, "id=".$this->id) or die('Update: '.$this->db->lastError);
		}
		else {
			$record[self::ORDER_FIELD_NAME] = self::getNextOrderIndex();
			$db->insert(self::TABLE, $record);
		}
	}

	static function getList() {
		$db = Registry::getInstance()->get('db');
		$rs = $db->query("SELECT * FROM `".self::TABLE."` ORDER BY `".self::ORDER_FIELD_NAME."`");
		$items = array ();
		while($sa = $db->fetch($rs)) {
			$items[] = $sa;
		}
		return $items;
	}

	static function delete($moduleId) {
		if ($id = intval($moduleId)) {
			$db = Registry::getInstance()->get('db');
			$rs = $db->query("SELECT `".self::ORDER_FIELD_NAME."` FROM `".self::TABLE."` WHERE `id`=$id");
			if ($sa = $db->fetch($rs)) {
				$db->query("DELETE FROM `".self::TABLE."` WHERE id=$id") or die('Delete module: '.$db->lastError);
				$db->update(
					self::TABLE,
					array(self::ORDER_FIELD_NAME => $db->makeForcedValue('`'.self::ORDER_FIELD_NAME.'`-1')),
					"`".self::ORDER_FIELD_NAME."`>={$sa[self::ORDER_FIELD_NAME]}"
				) or die('Renumber: '.$db->lastError);
			}
		}
	}

}
