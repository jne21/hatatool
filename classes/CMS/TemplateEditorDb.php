<?php
namespace CMS;

use common\Registry;

class TemplateEditorDb implements iTemplateEditor {
	const
		DB    = 'db',
		TABLE = 'template'
	;

	public $id, $name, $alias, $html;
	
	/**
	 * Создание экземпляра объекта из БД.
	 * @param string $templateAlias идентификатор шаблона
	 * @param string $dbName ключ для поиска объекта БД в Registry
	 * @param string $tableName имя таблицы в БД
	 * @param string $keyField имя поля, по которому происходит поиск идентификатора шаблона в БД
	 * @param string $valueField имя поля, содержащего шаблон
	 **/
	function __construct($id) {
		$db = Registry::getInstance()->get(self::DB);
		$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `id`=".intval($id));
		if ($sa = $db->fetch($rs)) {
			$this->id    = $sa['id'];
			$this->name  = $sa['name'];
			$this->alias = $sa['alias'];
			$this->html  = $sa['html'];
		}
	}

	function save() {
		$db = Registry::getInstance()->get(self::DB);
		$templateData = [
			'name'  => $this->name,
			'alias' => $this->alias,
			'html'  => $this->html
		];
		if ($this->id) {
			$db->update(
				self::TABLE,
				$templateData,
				'`id`='.intval($this->id)
			);
		}
		else {
			$db->insert(self::TABLE, $templateData);
		}
	}

	static function delete($id) {
		$db = Registry::getInstance()->get(self::DB);
		$db->query('DELETE FROM `'.self::TABLE.'` WHERE `id`='.intval($id));
	}
	
	static function getList() {
		$list = [];
		$db = Registry::getInstance()->get(self::DB);
		$db->query("SELECT * FROM `".self::TABLE."` ORDER BY `alias`");
		while ($sa = $db->fetch($rs)) {
			$list[$sa['id']] = $sa;
		}
		return $list;
	}

}