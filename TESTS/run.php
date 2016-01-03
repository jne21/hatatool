#!/usr/bin/php
<?php
	require('../inc/connect.php');
	$hr = PHP_EOL . str_repeat('=', 50). PHP_EOL . PHP_EOL;

use common\Registry;

echo $hr;
echo 'START UNIT TESTS'. PHP_EOL . PHP_EOL;
	require('admin.php');
	require('exercise.php');
	require('course.php');
	require('lesson.php');
echo $hr;
if ($registry->get('test_error')) {
	out ('********* Some ERRORS found. *********');
	exit(1);
}
else {
	out ('No errors.'.PHP_EOL);
}

function out($string, $error=NULL) {
	if ($error) Registry::getInstance()->set('test_error', true);
	echo $string . PHP_EOL;
}

function compare($value, $correctValue, $message) {
	if ($value !== $correctValue) {
		out($message . " received: ".gettype($value)."($value), correct: ".gettype($correctValue)."({$correctValue})", true);
		exit;
	}
}