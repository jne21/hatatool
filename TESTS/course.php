<?php
require_once('../inc/connect.php');

use CMS\Admin;
use WBT\Course;
use WBT\CourseL10n;
use common\Registry;


$owner = createAdmin();

$course = new Course;

$course->ownerId = $owner->id;
$course->state      = $state = 8;
$course->rights     = $rights = 1;

$locales = Registry::getInstance()->get('locales');

foreach (array_keys($locales) as $locale) {
	$l10n[$locale] = [
		'brief'       => 'brief 1 '.$locale,
		'description' => 'description 1'.$locale,
		'meta'        => 'meta 1'.$locale,
		'name'        => 'name 1'.$locale,
		'state'       => 0,
		'title'       => 'title 1'.$locale,
		'url'         => 'url 1'.rand().' '.$locale
	];
	$course->l10n->loadDataFromArray($locale, $l10n[$locale]);

}

out('WBT\Course');
out('WBT\Course->save() create');

$course->save();
if ($id = $course->id) {
	unset ($course);

out("WBT\Course->__construct($id)");

	$course1 = new Course($id);

	#print_r($course1);
	compare($course1->ownerId, $owner->id, "Invalid owner id ($id) after create.");
	compare($course1->state,   $state,     "Invalid state ($id) after create.");

	foreach ($l10n as $localeId=>$localeData) {
		foreach ($localeData as $field=>$value) {
			compare($course1->l10n->get($field, $localeId), $value, "Invalid ($id)->l10n($locale, $field) after create.");
		}
	}

	$course1->state      = $state = 1;
	$course1->rights     = $rights = 2;

	foreach (array_keys($locales) as $locale) {
		$l10n[$locale] = [
			'brief'       => 'brief 2 '.$locale,
			'description' => 'description 2'.$locale,
			'meta'        => 'meta 2'.$locale,
			'name'        => 'name 2'.$locale,
			'state'       => 1,
			'title'       => 'title 2'.$locale,
			'url'         => 'url 2 '.rand().' '.$locale
		];
	}

	foreach ($l10n as $localeId=>$localeData) {
		foreach ($localeData as $field=>$value) {
			$course1->l10n->set($field, $value, $localeId);
		}
	}

out("WBT\Course->save() update");

	$course1->save();
	unset($course1);

	$course2 = new Course($id);
	compare($course2->ownerId, $owner->id, "Invalid owner id ($id) after update.");
	compare($course2->state,   $state,     "Invalid state ($id) after update.");

	foreach ($l10n as $localeId=>$localeData) {
		foreach ($localeData as $field=>$value) {
			compare($course2->l10n->get($field, $localeId), $value, "Invalid ($id)->l10n($locale, $field) after update.");
		}
	}

	unset($course2);

out("WBT\Course::getList");

	$list = Course::getList();
	if (is_array($list)) {
		if (count($list)) {
			if (array_key_exists($id, $list)) {
				$course3 = $list[$id];
				if ($course3 instanceof Course) {
					compare($course3->ownerId, $owner->id, "getList: Invalid owner id ($id).");
					compare($course3->state,   $state,     "getList: Invalid state ($id).");

					foreach ($l10n as $localeId=>$localeData) {
						foreach ($localeData as $field=>$value) {
							compare($course3->l10n->get($field, $localeId), $value, "getList: Invalid ($id)->l10n($locale, $field).");
						}
					}

				}
				else {
					out ("getList item is not an instance of Course", true);
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
	unset ($course3);

out("WBT\Course::setState");

	Course::setState($id, 0);
	$course4 = new Course($id);

	compare($course4->state, 0, "Invalid value after setState ($id).");
	unset($course4);


out("WBT\Course::delete($id)");

	Course::delete($id);

	$course5 = new Course($id);
	if ($course5->id == $id) out("Delete does not working ($id)", true);

	$db = $registry->get(Course::DB);
	$rs = $db->query("SELECT COUNT(*) as `cnt` FROM `".CourseL10n::TABLE."` WHERE `parent_id`=$id");
	if ($sa = $db->fetch($rs)) {
		if ($sa['cnt']) out ("Delete does not remove localization data ($id)", true);
	}

}
else {
	out('Empty id after create', true);
}

	Admin::delete($owner->id);

out(PHP_EOL.'... passed.'.PHP_EOL);

function createCourse($ownerId) {
	$course = new Course;

	$course->ownerId = $ownerId;
	$course->state   = 8;
	$course->rights  = 1;

	$locales = Registry::getInstance()->get('locales');

	foreach (array_keys($locales) as $locale) {
		$l10n[$locale] = [
				'brief'       => 'brief 1 '.$locale,
				'description' => 'description 1'.$locale,
				'meta'        => 'meta 1'.$locale,
				'name'        => 'name 1'.$locale,
				'state'       => 0,
				'title'       => 'title 1'.$locale,
				'url'         => 'url 1'.rand().' '.$locale
		];
		$course->l10n->loadDataFromArray($locale, $l10n[$locale]);
	}
	$course->save();
	return $course;
}
