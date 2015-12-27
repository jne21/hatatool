<?
namespace common;

use common\Registry;

class L10n {
	const
		DB = 'db'
	;

	public
		$data
	;

	static function load($parentId=NULL, $parentTable) {
		$registry = Registry::getInstance();
		$db = $registry->get(self::DB);
		$result = [];
		if ($id = intval($parentId)) {
			$sql = "SELECT * FROM `$parentTable` WHERE `parent_id`=$id"; //die($sql);
			$rs = $db->query($sql);
			while ($sa = $db->fetch($rs)) {
				$result[] = $sa;
			}
		}
		return $result;
	}

	static function loadByParentIds($idList, $parentTable) {
		$result = [];
		if (is_array($idList) && count($idList)) {
			$registry = Registry::getInstance();
			$db = $registry->get(self::DB);
			$ids = array_map('intval', $idList);
			$sql = "SELECT * FROM `".$db->realEscapeString($parentTable)."` WHERE `parent_id` IN (".implode(',', $ids).")"; //die($sql);
			$rs = $db->query($sql);
			while ($sa = $db->fetch($rs)) {
				$result[$sa['parent_id']][$sa['locale_id']] = $sa;
			}
		}
		return $result;
	}

	static function saveData($parentId, $parentTable, $data) {
//d($data,1);
		$registry = Registry::getInstance();
		$db = $registry->get(self::DB);
		foreach(array_keys($registry->get('locales')) as $locale) {
			$db->replace (
				$db->realEscapeString($parentTable),
				array_merge(
					[
						'parent_id' => $parentId,
						'locale_id' =>$locale
					],
					$data[$locale]
				)
			);
		}
	}

	/**
	 * Получаем родителей по значению атрибута.
	 **/
	static function getParentIdsListByValue($parentTable, $field, $value, $locale=NULL) {
		$registry = Registry::getInstance();
		$db = $registry->get(self::DB);
		if (!$locale) {
			$locale = $registry()->get('i18n_language'); 
		}
		$result = [];
		$rs = $db->query("SELECT `parent_id` FROM `".$db->realEscapeString($parentTable)."` WHERE `".$db->realEscapeString($field)."`=".$db->escape($value));
		while ($sa = $db->fetch($rs)) {
			$result[] = $sa['parent_id'];
		}
		return $result;
	}

	static function checkValueOriginality($parentTable, $field, $value, $locale=NULL) {
		$registry = Registry::getInstance();
		$db = $registry->get(self::DB);
		if (!$locale) {
			$locale = $registry()->get('i18n_language'); 
		}
		$rs = $db->query("SELECT IFNULL(COUNT(*), 0) FROM `".$db->realEscapeString($parentTable)."` WHERE `".$db->realEscapeString($field)."`=".$db->escape($value));
		return $db->result($rs, 0, 0)==0;
	}

	function get($field, $locale = NULL) {
		if (!$locale) {
			$locale = Registry::getInstance()->get('i18n_language'); 
		}
		return $this->data[$locale][$field];
	}

	function set($field, $value, $locale = NULL) {
		if (!$locale) {
			$locale = Registry::getInstance()->get('i18n_language'); 
		}
		$this->data[$locale][$field] = $value;
	}

	static function deleteAll($parentTable, $parentId) {
		$db = Registry::getInstance()->get(self::DB);
		$db->query("DELETE FROM `".$db->realEscapeString($parentTable)."` WHERE `parent_id`=".$db->escape($parentId));
	} 
}
?>