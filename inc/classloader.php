<?php
spl_autoload_register(function ($class) {
#	echo "\n\n\n[$class]\n\n\n";
	require (common\Registry::getInstance()->get('site_class_path') . str_replace('\\', DIRECTORY_SEPARATOR,  $class) . '.php');
});