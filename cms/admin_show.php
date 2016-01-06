<?php

require('inc/authent.php');
if (!$demo_mode) {
	Admin::toggle(
		intval($_GET['id']),
		intval($_GET['act'])
	);
}
header('Location: '.$_SERVER['HTTP_REFERER']);
