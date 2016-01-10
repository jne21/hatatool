<?php
	require('inc/connect.php');
	$only_admin = true;
	$access_admin = USER_ADMIN;
	require('inc/authent.php');

	define('TABLE', 'module');

	if (!$demo_mode) {

		$id = intval($_GET['id']);
		$act = $_GET['act'];

		$rs = $db->query("SELECT * FROM ".TABLE." WHERE `id`=$id") or die('Get: '.db_lastError);
		$sa = $db->fetch($rs);

		$oldnum    = $sa['num'];

		$newnum = $oldnum + ($act=='dn'?1:-1);

		$db->update(TABLE, array('num' => $newnum), "id=$id") or die('UPD1: '.$db->lastError);
		$db->update(TABLE, array('num' => $oldnum), "`num`=$newnum AND id<>$id") or die('UPD2: '.$db->lastError);
	}
	header('Location: '.$_SERVER['HTTP_REFERER']);
