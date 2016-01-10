<?php

require("inc/connect.php");
unset($_SESSION['admin']);
header('Location: index.php');
