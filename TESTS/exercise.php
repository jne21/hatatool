<?php
require_once('../inc/connect.php');

use common\Registry;
use WBT\Exercise;

$exercise = new Exercise();

$exercise->name        = $name        = "unit test ".date('Y-m-d H:i:s');
$exercise->description = $description = "unit test description";
$exercise->script      = $script      = "unit_test_script.php";

out('WBT\Exercise');
out('WBT\Exercise->save() create');

$exercise->save();
if ($id = $exercise->id) {
	unset ($exercise);

out("WBT\Exercise->__construct($id)");

	$exercise1 = new Exercise($id);

	#print_r($exercise1);
	compare($exercise1->name,        $name,        "Invalid name ($id) after create.");
	compare($exercise1->description, $description, "Invalid description ($id) after create.");
	compare($exercise1->script,      $script,      "Invalid script ($id) after create.");
	
	$exercise1->name        = $name        = "unit test 1";
	$exercise1->description = $description = "unit test description 1";
	$exercise1->script      = $script      = "unit_test_script_1.php";
	
out("WBT\Exercise->save() update");

	$exercise1->save();
	unset($exercise1);

	$exercise2 = new Exercise($id);
	compare($exercise2->name,        $name,        "Invalid name ($id) after update.");
	compare($exercise2->description, $description, "Invalid description ($id) after update.");
	compare($exercise2->script,      $script,      "Invalid script ($id) after update.");
	
	unset($exercise2);

out("WBT\Exercise->save() getList");

	$list = Exercise::getList();
	if (is_array($list)) {
		if (count($list)) {
			if (array_key_exists($id, $list)) {
				$exercise3 = $list[$id];
				if ($exercise3 instanceof Exercise) {
					compare($exercise3->name,        $name,        "getList: Invalid name ($id) after update.");
					compare($exercise3->description, $description, "getList: Invalid description ($id) after update.");
					compare($exercise3->script,      $script,      "getList: Invalid script ($id) after update.");
				}
				else {
					out ("getList item is not an instance of Exercise", true);
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
	unset ($exercise3);

out("WBT\Exercise::delete($id)");

	Exercise::delete($id);

	$exercise5 = new Exercise($id);
	if ($exercise5->id == $id) out("Delete does not working ($id)", true);

}
else {
	out('Empty id after create', true);
}

out(PHP_EOL.'... passed.'.PHP_EOL);

function createExercise() {
	$exercise = new Exercise();

	$exercise->name        = $name        = "unit test ".date('Y-m-d H:i:s');
	$exercise->description = $description = "unit test description";
	$exercise->script      = $script      = "unit_test_script.php";
	$exercise->save();

	return $exercise;
}
