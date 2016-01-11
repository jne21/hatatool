<?php

$auth_required = true;
require('inc/authent.php');

use common\Redirect;
Redirect::purge($id = intval($_GET['id']));

header("Location: redirect_edit.php?id=$id");
