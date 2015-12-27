<?php
namespace WBT;

use \common\Registry;

final class Exercise {

	const
		TABLE = 'exercise',
		DB    = 'db'
	;

	public
		$id, $name, $description, $script;

	function __construct($entityId = NULL) {
		if ($id = intval($entityId)) {
			$db = Registry::getInstance()->get(self::DB);
			$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `id`=$id");
			if ($sa = $db->fetch($rs)) {
				$this->loadDataFromArray($sa);
			}
		}
	}
	
	function loadDataFromArray($data) {
		$this->id          = $data['id'];
		$this->name        = $data['name'];
		$this->description = $data['description'];
		$this->script      = $data['script'];
	}
	
	function save() {
		$db = Registry::getInstance()->get(self::DB);
		$record = [
				'name'        => $this->name,
				'description' => $this->description,
				'script'      => $this->script
		];
		if ($this->id) {
			$db->update (self::TABLE, $record, "`id`=".intval($this->id));
		}
		else {
			$db->insert(self::TABLE, $record);
			$this->id = $db->insertId();
		}
	}
	
	static function getList() {
		$result = [];
		$db = Registry::getInstance()->get(self::DB);
		$rs = $db->query("SELECT * FROM `".self::TABLE."` ORDER BY `name`");
		while ($sa = $db->fetch($rs)) {
			$entity = new Exercise;
			$entity->loadDataFromArray($sa);
			$result[$sa['id']] = $entity;
		}
		return $result;
	}
	
	static function delete($entityId) {
		if ($id = intval($entityId)) {
			$db = Registry::getInstance()->get(self::DB)->query("DELETE FROM `".self::TABLE."` WHERE `id`=$id");
		}
	}

}