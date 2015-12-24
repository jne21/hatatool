<?
//	if ($_SERVER['HTTP_HOST']!='.com') {
////header("HTTP/1.1 301 Moved Permanently");
//		header("Location: http://domain.com{$_SERVER['REQUEST_URI']}", TRUE, 301);
//		exit;
//	}

//	header("Content-Type: text/html; charset=UTF-8", TRUE);

	session_start();
	require('variables.php');

	use DB\Mysqli as db;

	$db_TZ = date('I') ? '+03:00' : '+02:00';

	$db  = new db(DB_SERVER,  DB_USER,  DB_PASSWORD,  DB_NAME);
	if ($db->lastError) die($db->lastError);
	$db->query('SET NAMES utf8');
	$db->query("SET time_zone = '$db_TZ'");
	$registry->set('db',  $db);

	require('variables_common.php');

?>
