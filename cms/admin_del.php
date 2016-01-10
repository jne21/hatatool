<?
$auth_required = TRUE;
require('inc/authent.php');

use CMS\Admin;

Admin::delete(intval($_GET['id']));

header('Location: '.$_SERVER['HTTP_REFERER']);
exit;
?>