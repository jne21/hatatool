<?php
$auth_required = TRUE;
require('inc/authent.php');

Admin::toggle(
	intval($_GET['id']),
	intval($_GET['act'])
);
header('Location: '.$_SERVER['HTTP_REFERER']);
