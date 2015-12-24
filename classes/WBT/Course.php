<?php
namespace WBT;

use common\Registry;
#use Lesson;
#use Stage;
#use Module;
#use Exercise;

final class Course {

	const
		TABLE = 'course',
		DB    = 'db'
	;

	public
		$id, $ownerId, $dateCreate, $dateUpdate, $state, $order, $l10n;

	use \common\entity;

	function __construct($courseId=NULL) {
	}

	function loadDataFormArray($data) {
	}

	function save() {
	}

	static function getList() {
	}

	static function delete($courseId) {
	}

	static function setState($courseId, $action) {
	}

}