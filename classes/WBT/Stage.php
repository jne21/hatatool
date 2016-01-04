<?php
namespace WBT;
use common\Registry;
use WBT\StageL10n;

class Stage {

	public
		/**
 		* Идентификатор этапа
 		* @var int
 		*/
		$id,

		/**
		 * Идентификатор урока
		 * @var int
		 */
		$lessonId,

		/**
		 * Идентификатор упражнения
		 * @var int
		 */
		$exerciseId,

		/**
		 * Порядковый номер для ручной сортировки
		 * @var int
		 */
		$order,

		/**
		 * Настроечные параметры для упражнения
		 * @var string
		 */
		$settings,

		/**
		 * Локализация
		 * @var string
		 */
		$l10n
	;
	
	const
		DB    = 'db',
		TABLE = 'stage',
		
		ORDER_FIELD_NAME = 'order'
	;

use \common\entity;

/**
 * Создание экземпляра класса Stage
 * @param string $stageId - код Этапа. Необязательно.
 */
function __construct($stageId = NULL) {
	if ($id = intval($stageId)) {
		$db = Registry::getInstance()->get(self::DB);
		$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `id`=$id");
		if ($sa = $db->fetch($rs)) {
			$this->loadDataFromArray($sa);
		}
	}
	$this->l10n  = new StageL10n($this->id);
}

/**
 * Загрузка свойств из массива
 * @param unknown $data
 */
function loadDataFromArray($data) {
	$this->id         = intval($data['id']);
	$this->lessonId   = intval($data['lesson_id']);
	$this->exerciseId = intval($data['exercise_id']);
	$this->name       = $data['name'];
	$this->order      = intval($data[self::ORDER_FIELD_NAME]);
	$this->settings   = $data['settings'];
}

/**
 * Получение списков этапов заданного урока
 * @param unknown $lessonId
 * @return array
 */
static function getList($lessonId) {
	$result = [];
	$db = Registry::getInstance()->get(self::DB);
	
	$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `lesson_id`=".intval($lessonId)." ORDER BY `".self::ORDER_FIELD_NAME."`");
	while ($sa = $db->fetch($rs)) {
		$stage = new Stage();
		$stage->loadDataFromArray($sa);
		$result[$sa['id']] = $stage;
	}
	$l10nList = StageL10n::getListByIds(array_keys($result));
	foreach ($result as $stageId=>$stage) {
		$result[$stageId]->l10n = $l10nList[$stageId];
	}
	return $result;
}

/**
 * Сохранение объекта в БД при добавлении или редактировании
 */
function save() {
	$db = Registry::getInstance()->get(self::DB);
	$properties = [
		'lesson_id' => $this->lessonId,
		'exercise_id' => $this->exerciseId,
		'name'        => $this->name,
		'settings'    => $this->settings
	];
	if ($this->id) {
		$db->update (self::TABLE, $properties, "`id`=".intval($this->id));
	}
	else {
		$this->order = self::getNextOrderIndex();
		$properties[self::ORDER_FIELD_NAME] = $this->order;
		$db->insert(self::TABLE, $properties);
		$this->id = $db->insertId();
		$this->l10n->parentId = $this->id;
	}
	$this->l10n->save();
}



static function delete($stageId) {
	$stage = new Stage($stageId);
	if ($stage->id) {
		StageL10n::deleteAll(StageL10n::TABLE, $stage->id);
		$db = Registry::getInstance()->get(self::DB);
		$db->query("DELETE FROM `".self::TABLE."` WHERE `id`=".$stage->id) or die($db->lastError);
		$db->update(
				self::TABLE,
				[self::ORDER_FIELD_NAME => $db->makeForcedValue("`".self::ORDER_FIELD_NAME."`-1")],
				'`lesson_id`='.$stage->lessonId.' AND `'.self::ORDER_FIELD_NAME.'`>'.$stage->order
		);
	}
	
}

}