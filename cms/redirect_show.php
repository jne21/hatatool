<?php
$auth_required = true;
require('inc/authent.php');

use common\Redirect;

Redirect::toggle(intval($_GET['id']), intval($_GET['act']!=0), 'active');
header('Location: redirect.php');
