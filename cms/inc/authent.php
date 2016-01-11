<?php

require_once('connect.php');

//echo '<pre>'.print_r($registry, TRUE).'</pre>';
if ($auth_required == true) {
    if (! $_SESSION['admin']['id']) {
        header('Location: /cms/login.php');
    }
}
