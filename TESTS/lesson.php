<?php
require_once('../inc/connect.php');

use CMS\Admin;
use WBT\Course;
use WBT\CourseL10n;
use common\Registry;
use WBT\Lesson;
use WBT\LessonL10n;
use WBT\Stage;

out('WBT\Lesson');

$owner = createAdmin();
$course = createCourse($owner->id);

$lesson = new Lesson();

$lesson->courseId = $course->id;
$lesson->name     = $name = "name 1";

$locales = Registry::getInstance()->get('locales');

foreach (array_keys($locales) as $locale) {
	$l10n[$locale] = [
		'brief'       => 'brief 1 '.$locale,
		'description' => 'description 1'.$locale,
		'meta'        => 'meta 1'.$locale,
		'name'        => 'name 1'.$locale,
		'title'       => 'title 1'.$locale,
		'url'         => 'url 1'.rand().' '.$locale
	];
	$lesson->l10n->loadDataFromArray($locale, $l10n[$locale]);
}

out('WBT\Lesson->save() create');

$lesson->save();

if ($id = $lesson->id) {
	unset ($lesson);

out("WBT\Lesson->__construct($id)");

	$lesson1 = new Lesson($id);

	#print_r($course1);
	compare($lesson1->courseId, $course->id, "Invalid course id ($id) after create.");
	compare($lesson1->name,     $name,       "Invalid name ($id) after create.");

	foreach ($l10n as $localeId=>$localeData) {
		foreach ($localeData as $field=>$value) {
			compare($lesson1->l10n->get($field, $localeId), $value, "Invalid ($id)->l10n($locale, $field) after create.");
		}
	}

	$lesson1->name      = $name = "name 2";

	foreach (array_keys($locales) as $locale) {
		$l10n[$locale] = [
			'brief'       => 'brief 2 '.$locale,
			'description' => 'description 2'.$locale,
			'meta'        => 'meta 2'.$locale,
			'name'        => 'name 2'.$locale,
			'title'       => 'title 2'.$locale,
			'url'         => 'url 2 '.rand().' '.$locale
		];
	}

	foreach ($l10n as $localeId=>$localeData) {
		foreach ($localeData as $field=>$value) {
			$lesson1->l10n->set($field, $value, $localeId);
		}
	}

out("WBT\Course->save() update");

	$lesson1->save();
	unset($lesson1);

	$lesson2 = new Lesson($id);
	compare($lesson2->courseId, $course->id, "Invalid course id ($id) after update.");
	compare($lesson2->name,     $name,       "Invalid name ($id) after update.");

	foreach ($l10n as $localeId=>$localeData) {
		foreach ($localeData as $field=>$value) {
			compare($lesson2->l10n->get($field, $localeId), $value, "Invalid ($id)->l10n($locale, $field) after update.");
		}
	}

	unset($lesson2);

out("WBT\Lesson::getList");

	$list = Lesson::getList($course->id);
	if (is_array($list)) {
		if (count($list)) {
			if (array_key_exists($id, $list)) {
				$lesson3 = $list[$id];
				if ($lesson3 instanceof Lesson) {
					compare($lesson3->courseId, $course->id, "getList: Invalid course id ($id).");
					compare($lesson3->name,    $name,      "getList: Invalid state ($id).");

					foreach ($l10n as $localeId=>$localeData) {
						foreach ($localeData as $field=>$value) {
							compare($lesson3->l10n->get($field, $localeId), $value, "getList: Invalid ($id)->l10n($locale, $field).");
						}
					}

				}
				else {
					out ("getList item is not an instance of Lesson", true);
				}
			}
			else {
				out ("getList returns empty array from not empty database ($id)", true);
			}
		}
		else {
			out("getList return value is not an array", true);
		}
	}
	unset ($lesson3);

out("WBT\Lesson::delete($id)");

	Lesson::delete($id);

	$lesson5 = new Lesson($id);

	if ($lesson5->id == $id) out("Delete does not working ($id)", true);

	$db = $registry->get(Lesson::DB);
	$rs = $db->query("SELECT COUNT(*) as `cnt` FROM `".LessonL10n::TABLE."` WHERE `parent_id`=$id");
	if ($sa = $db->fetch($rs)) {
		if ($sa['cnt']) out ("Delete does not remove localization data ($id)", true);
	}
	$rs = $db->query("SELECT COUNT(*) as `cnt` FROM `".Stage::TABLE."` WHERE `lesson_id`=$id");
	if ($sa = $db->fetch($rs)) {
		if ($sa['cnt']) out ("Delete does not remove stages ($id)", true);
	}
	
}
else {
	out('Empty id after create', true);
}

Course::delete($course->id);
Admin::delete($owner->id);

out(PHP_EOL.'... passed.'.PHP_EOL);

function createLesson($courseId) {
	$lesson = new Lesson();
	
	$lesson->courseId = $courseId;
	$lesson->name     = $name = "name 1";
	
	$locales = Registry::getInstance()->get('locales');
	
	foreach (array_keys($locales) as $locale) {
		$l10n[$locale] = [
			'brief'       => 'brief 1 '.$locale,
			'description' => 'description 1'.$locale,
			'meta'        => 'meta 1'.$locale,
			'name'        => 'name 1'.$locale,
			'title'       => 'title 1'.$locale,
			'url'         => 'url 1'.rand().' '.$locale
		];
		$lesson->l10n->loadDataFromArray($locale, $l10n[$locale]);
	}
	
	$lesson->save();
	return $lesson;
}
