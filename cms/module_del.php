<?php
$auth_required = TRUE;
require('inc/authent.php');

use CMS\Module;
Module::delete(intval($_GET['id']);
header('location: module.php');
