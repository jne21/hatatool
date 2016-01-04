<?
namespace common;

use common\Registry;

class L10n {
	const
		DB = 'db'
	;

	/**
	 * Хранилище массива локализации
	 * @var array
	 */
	public
		$data
	;

	/**
	 * Создание экземпляра объекта локализации
	 * @param string $tableName Имя таблицы 
	 * @param int $parentId Идентификатор родительской сущности 
	 */
	function __construct($tableName, $parentId = NULL) {
		if ($id = intval($parentId)) {
			$this->parentId = $id;
		}
		foreach (self::load($tableName, intval($id)) as $item) {
			$this->loadDataFromArray($item['locale_id'], $item);
		}
	}

	/**
	 * Загрузка из БД данных локализации заданной сущности
	 * @param string $parentTable Имя таблицы БД для загрузки локализации
	 * @param int $parentId Идентификатор родителя
	 * @return array
	 */
	protected static function load($parentTable, $parentId=NULL) {
		$registry = Registry::getInstance();
		$db = $registry->get(self::DB);
		$result = [];
		if ($id = intval($parentId)) {
			$sql = "SELECT * FROM `$parentTable` WHERE `parent_id`=$id";
			$rs = $db->query($sql);
			while ($sa = $db->fetch($rs)) {
				$result[] = $sa;
			}
		}
		return $result;
	}

	/**
	 * Получение массива с данными локализации для группы сущностей
	 * @param string $parentTable Имя таблицы БД
	 * @param int[] $idList Массив идентификаторов родителей
	 * @return array
	 */
	static function loadByParentIds($parentTable, $idList) {
		$result = [];
		if (is_array($idList) && count($idList)) {
			$registry = Registry::getInstance();
			$db = $registry->get(self::DB);
			$ids = array_map('intval', $idList);
			$sql = "SELECT * FROM `".$db->realEscapeString($parentTable)."` WHERE `parent_id` IN (".implode(',', $ids).")";
			$rs = $db->query($sql);
			while ($sa = $db->fetch($rs)) {
				$result[$sa['parent_id']][$sa['locale_id']] = $sa;
			}
		}
		return $result;
	}

	/**
	 * Сохранение данных в БД
	 * @param int $parentId Идентификатор родителя
	 * @param string $parentTable Имя таблицы БД 
	 * @param array $data
	 */
	static function saveData($parentId, $parentTable, $data) {
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
	 * Получаем массив идентификаторов родителей по значению атрибута.
	 * @param string $parentTable Имя таблицы БД
	 * @param string $field Имя поля локализации
	 * @param string $value Присваиваемое значение
	 * @param string $locale Идентификатор локализации. По умолчанию системный язык.
	 * @return integer[]
	 */
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

	/**
	 * Проверка уникальности значения для поля.
	 * @param string $parentTable Имя таблицы БД
	 * @param string $field Имя поля локализации
	 * @param string $value Проверяемое значение
	 * @param string $locale Идентификатор локализации
	 * @return boolean
	 */
	static function checkValueOriginality($parentTable, $field, $value, $locale=NULL) {
		$registry = Registry::getInstance();
		$db = $registry->get(self::DB);
		if (!$locale) {
			$locale = $registry()->get('i18n_language'); 
		}
		$rs = $db->query("SELECT IFNULL(COUNT(*), 0) FROM `".$db->realEscapeString($parentTable)."` WHERE `".$db->realEscapeString($field)."`=".$db->escape($value));
		return $db->result($rs, 0, 0)==0;
	}

	/**
	 * Получение значения локализации заданного поля 
	 * @param string $field Имя поля
	 * @param string $locale Идентификатор локализации
	 */
	function get($field, $locale = NULL) {
		if (!$locale) {
			$locale = Registry::getInstance()->get('i18n_language'); 
		}
		return $this->data[$locale][$field];
	}

	/**
	 * Установка значения локализации заданного поля
	 * @param string $field Имя поля
	 * @param string $value Присваимое значение
	 * @param string $locale Идентификатор локализации
	 */
	function set($field, $value, $locale = NULL) {
		if (!$locale) {
			$locale = Registry::getInstance()->get('i18n_language'); 
		}
		$this->data[$locale][$field] = $value;
	}

	/**
	 * Удаление локализации заданной сущности
	 * @param string $parentTable Имя таблицы БД
	 * @param int $parentId Идентификатор родителя
	 */
	static function deleteAll($parentTable, $parentId) {
		$db = Registry::getInstance()->get(self::DB);
		$db->query("DELETE FROM `".$db->realEscapeString($parentTable)."` WHERE `parent_id`=".$db->escape($parentId));
	} 
}
