<?php

$auth_required = true;
require('inc/authent.php');

use common\Redirect;
Redirect::delete(intval($_GET['id']));
header('location: redirect.php');
