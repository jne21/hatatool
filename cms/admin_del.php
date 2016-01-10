<?
    use CMS\Admin;
    require('inc/authent.php');

	if (!$demo_mode) {
		Admin::delete(intval($_GET['id']));
	}
	header('Location: '.$_SERVER['HTTP_REFERER']);
    exit;
?>