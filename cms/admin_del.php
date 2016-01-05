<?
	require('inc/authent.php');
	require($site_include_path.'removedir.php');

	if (!$demo_mode) {
		$id = intval($_GET['id']);
		removedir($site_root_absolute.'img/prod/'.$id);
		$db->query("DELETE FROM `media` WHERE `parent_id`=$id AND `parent_type`=2");
		$db->query("DELETE FROM `delivery` WHERE `prod_id`=$id");
		$db->query("DELETE FROM `prod` WHERE id=$id");
	}
	header('Location: '.$_SERVER['HTTP_REFERER']);
?>