<?php
namespace WBT;

use \common\Registry;
use WBT\LessonL10n;
#use Lesson;
#use Stage;
#use Stage;
#use Module;
#use Exercise;

final class Lesson {

	const
		TABLE = 'lesson',
		DB    = 'db',
		
		HIDDEN = 0,
		VISIBLE = 1,

		ORDER_FIELD_NAME = 'order'
	;

	public
		$id, $courseId, $name, $order, $l10n;

	use \common\entity;

	function __construct($courseId = NULL, $ignoreHidden = self::VISIBLE) {
		if ($id = intval($courseId)) {
			$db = Registry::getInstance()->get(self::DB);
			$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `id`=$id");
			if ($sa = $db->fetch($rs)) {
				$this->loadDataFromArray($sa);
			}
		}
		$this->l10n  = new LessonL10n($this->id);
	}
	
	function loadDataFromArray($data) {
		$this->id         = intval($data['id']);
		$this->courseId   = intval($data['course_id']);
		$this->name       = $data['name'];
		$this->order      = intval($data[self::ORDER_FIELD_NAME]);
	}
	
	function save() {
		$db = Registry::getInstance()->get(self::DB);
		if ($this->id) {
			$this->dateUpdate = time();
			$db->update (
					self::TABLE,
					[
							'name'        => $this->name
					],
					"`id`=".intval($this->id)
			);
		}
		else {
			$this->num = self::getNextOrderIndex();
			$db->insert(
					self::TABLE,
					[
							'course_id'             => $this->courseId,
							'name'                  => $this->name,
							self::ORDER_FIELD_NAME  => self::getNextOrderIndex()
					]
			) or die($db->lastError);
			$this->id = $db->insertId();
			$this->l10n->parentId = $this->id;
		}
		$this->l10n->save();
	}
	
	static function getList($courseId) {
		$result = [];
		$db = Registry::getInstance()->get(self::DB);
	
		$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `course_id`=".intval($courseId)." ORDER BY `".self::ORDER_FIELD_NAME."`");
		while ($sa = $db->fetch($rs)) {
			$lesson = new Lesson();
			$lesson->loadDataFromArray($sa);
			$result[$sa['id']] = $lesson;
		}
		$l10nList = LessonL10n::getListByIds(array_keys($result));
		foreach ($result as $lessonId=>$lesson) {
			$result[$lessonId]->l10n = $l10nList[$lessonId];
		}
		return $result;
	}
	
	static function delete($lessonId) {
		$lesson = new Lesson($lessonId);
		if ($lesson->id) {
			LessonL10n::deleteAll(LessonL10n::TABLE, $lesson->id);
#			Stage::deleteAll($lesson->id);
			$db = Registry::getInstance()->get(self::DB);
			$db->query("DELETE FROM `".self::TABLE."` WHERE `id`=".$lesson->id) or die($db->lastError);
			$db->update(
				self::TABLE,
				[self::ORDER_FIELD_NAME => $db->makeForcedValue("`".self::ORDER_FIELD_NAME."`-1")],
				'`course_id`='.$lesson->courseId.' AND `'.self::ORDER_FIELD_NAME.'`>'.$lesson->order
			);
		}
	}
	
	static function setState($courseId, $state) {
		self::updateValue($courseId, 'state', $state);
	}

}