<?php

require_once('connect.php');

use system\LoginError;
use common\TemplateFile as Template;
use CMS\Admin;

$message = '';
//echo '<pre>'.print_r($registry, TRUE).'</pre>';
if ($auth_required == true) {
    if (! $_SESSION['admin']['id']) {
        if ($_POST['action']=='login') {
            if ($_POST['login'].$_POST['password']) {
                if (LoginError::isBlocked()) {
					$message = 'Перевищено ліміт помилок входу.<br />Вас заблоковано на 30 хвилин.';
				}
				else {
                    $admin = Admin::getInstance($_POST['login'], $_POST['password']);
					if ($admin->id) {
					    $admin->dateLogin = time();
						$admin->save();
					    $_SESSION['admin'] = [
					            'id' => $admin->id,
					            'locale' => $admin->locale,
					            'name' => $admin->name
					    ];
						unset($_SESSION['login_error']);
						// header("Location: index.php"); // uncomment to disable pass-through authentification
					}
					else {
						LoginError::register($_POST['login'], $_POST['password']);
						$limit = LOGIN_ERROR_LIMIT-1-$sae['cnt'];
						$message = $i18n->getText('login_error');
						$_SESSION['login_error'] = 1;
					}
				}
			}
			else $message = $i18n->getText('input_error');
		}
		if (intval($_SESSION['admin']['id'])==0 || $message!='') {
			$tpl = new Template($registry->get('cms_template_path').'login.htm');
			echo $tpl->apply(
				array (
					'message'          => $message,
					'background-image' => $_SESSION['login_error'] ? 'UPA.jpg' : 'UA.jpg'
				)
			);
			exit;
		}
		else header('Location: index.php');
	}
	if ($access_admin && (! ($access_admin & $_SESSION['admin_rights']))) {
		header('HTTP/1.1 404 Not found', TRUE);
		header('Status: 404 Not found'/*, TRUE*/);
		echo 'Access denied: Page not found.';
		exit;
	}
}
