<?php

require_once('connect.php');

$message = '';
//echo '<pre>'.print_r($registry, TRUE).'</pre>';
if ($auth_required == true) {
    if (! $_SESSION['admin']['id']) {
        header('Location: /cms/login.php');
    }
	if ($access_admin && (! ($access_admin & $_SESSION['admin_rights']))) {
		header('HTTP/1.1 404 Not found', TRUE);
		header('Status: 404 Not found'/*, TRUE*/);
		echo 'Access denied: Page not found.';
		exit;
	}
}
