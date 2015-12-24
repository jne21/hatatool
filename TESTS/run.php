#!/usr/bin/php
<?php
	require('../inc/connect.php');
	$hr = PHP_EOL . str_repeat('=', 50). PHP_EOL . PHP_EOL;

use common\Registry;

echo $hr;
echo 'START UNIT TESTS'. PHP_EOL . PHP_EOL;
	require('admin.php');
#	require('course.php');
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