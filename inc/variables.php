<?php
	ini_set ('error_reporting', E_ALL & ~E_NOTICE);
//	ini_set ('display_errors', 1);

	if (php_sapi_name()=='cli') {
		$site_root_absolute = realpath(__DIR__ . DIRECTORY_SEPARATOR .'..') . DIRECTORY_SEPARATOR;
	}
	else {
		$site_root_absolute = $_SERVER["DOCUMENT_ROOT"];//.'/';
	}
#die($site_root_absolute);
	$site_class_path    = $site_root_absolute.'classes/';
	$site_include_path  = $site_root_absolute.'common/';
	$site_module_path   = $site_root_absolute.'modules/';

	require($site_class_path.'system/Exception.php');
	use system\ExceptionHandler;
	$errorHandler = new ExceptionHandler;
	$errorHandler->setupHandlers();
	$errorHandler->debug = true;

	date_default_timezone_set('Europe/Kiev');


	require($site_class_path.'common/Registry.php');
	use common\Registry as Registry;


	$registry = Registry::getInstance();

	$registry->set('i18n_language', 'uk');
	
	$registry->set('site_root_absolute',   $site_root_absolute);
	$registry->set('site_class_path',      $site_class_path);
	$registry->set('site_template_path',   $site_root_absolute.'tpl/');
	$registry->set('site_image_path',      $site_root_absolute.'img/');
//	$registry->set('site_attachment_path', $site_root_absolute.'attachments/');

	$registry->set('site_attachment_root', $site_root_absolute.'attachments/');

	$registry->set('site_css_root', $site_root_absolute.'css/');
	$registry->set('site_js_root', $site_root_absolute.'js/');

	$registry->set('counters_enabled',     true);
	$registry->set('site_upload_absolute', $site_root_absolute.'72ff400718ab29040ef07c280c7c666c/');

	require('classloader.php');

//--------------------------------------
	define ('DB_SERVER',   'localhost');
	define ('DB_NAME',     'WBT');
	define ('DB_USER',     'wbt');
	define ('DB_PASSWORD', 'qyHFvQ');
//--------------------------------------


	