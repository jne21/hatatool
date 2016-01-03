<?php

namespace CMS;

use common\Registry;

class TemplateDb extends Template {

	const
		DEFAULT_DB          = 'db',
		DEFAULT_TABLE       = 'template',
		DEFAULT_FIELD_KEY   = 'alias',
		DEFAULT_FIELD_VALUE = 'html'
	;

	/**
	 * Создание экземпляра объекта из БД.
	 * @param string $templateAlias идентификатор шаблона 
	 * @param string $dbName ключ для поиска объекта БД в Registry
	 * @param string $tableName имя таблицы в БД
	 * @param string $keyField имя поля, по которому происходит поиск идентификатора шаблона в БД
	 * @param string $valueField имя поля, содержащего шаблон
	 */
	function __construct(
		$templateAlias,
		$dbName=self::DEFAULT_DB,
		$tableName=self::DEFAULT_TABLE,
		$keyField=self::DEFAULT_FIELD_KEY,
		$valueField=self::DEFAULT_FIELD_VALUE
	) {
		$db = registry::getInstance()->get('db');
		$rs = $db->query("SELECT * FROM `".$db->realEscapeString($tableName)."` WHERE `".$db->realEscapeString($keyField)."`=".$db->escape($templateAlias));
		if ($sa = $db->fetch($rs)) {
			$this->tpl = $sa[$setup['field_value']];
		}
		else {
			echo(__METHOD__."($tableName.$keyField=$templateAlias) Alias not found.");
		}
		$db->free($rs);
	}
}
