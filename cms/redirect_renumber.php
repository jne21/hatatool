<?php

$auth_required = TRUE;
require('inc/authent.php');

use common\Redirect;

Redirect::renumberAll($_POST['order']);
echo 'OK';
