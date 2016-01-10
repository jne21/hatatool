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
		TABLE = 'module';

	protected
		$db;

	function __construct($moduleId) {
		$this->db = Registry::getInstance()->get('db');
		if ($id = intval($moduleId)) {
			$rs = $this->db->query("SELECT * FROM `".self::TABLE."` WHERE id=$id") or die('Fetch: '.$this->db->lastError);
			if ($sa = $this->db->fetch($rs)) {
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
			$record['num'] = 0;
			$this->db->insert(self::TABLE, $record) or die('Insert: '.$this->db->lastError);
			$this->db->update(
				self::TABLE,
				array (
					'num' => $this->db->makeForcedValue('`num`+1')
				),
				''
			) or die('Update: '.$this->db->lastError);
		}
	}

	static function getList() {
		$db = Registry::getInstance()->get('db');
		$rs = $db->query("SELECT * FROM `".self::TABLE."` ORDER BY `num`") or die('M1: '.$db->lastError);
		$items = array ();
		while($sa = $db->fetch($rs)) {
			$items[] = $sa;
		}
		return $items;
	}

	static function delete($moduleId) {

		if ($id = intval($moduleId)) {
			$db = Registry::getInstance()->get('db');
			$rs = $db->query("SELECT `num` FROM `".self::TABLE."` WHERE `id`=$id") or die('Select: '.$db->lastError);
			if ($sa = $db->fetch($rs)) {
				$db->query("DELETE FROM `".self::TABLE."` WHERE id=$id") or die('Delete module: '.$db->lastError);
				$db->update(
					self::TABLE,
					array('num'=>$db->makeForcedValue('`num`-1')),
					"`num`>={$sa['num']}"
				) or die('Renumber: '.$db->lastError);
			}
		}
	}

	static function move($moduleId, $action) {

		if ($id = intval($moduleId)) {
			$db = Registry::getInstance()->get('db');
			$act = intval($action);

			$rs = $db->query("SELECT * FROM ".self::TABLE." WHERE `id`=$id") or die('Get: '.db_lastError);
			$sa = $db->fetch($rs);

			$oldnum = $sa['num'];
			$newnum = $oldnum + ($act ? 1 : -1);

			$db->update(self::TABLE, array('num' => $newnum), "id=$id") or die('UPD1: '.$db->lastError);
			$db->update(selg::TABLE, array('num' => $oldnum), "`num`=$newnum AND id<>$id") or die('UPD2: '.$db->lastError);
		}
	}

}
