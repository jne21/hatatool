<?php

$auth_required = TRUE;
require('inc/authent.php');

use CMS\Module;

Module::renumberAll($_POST['order']);
echo 'OK';
