<?php
	require_once('connect.php');

	use system\LoginError;
//$_SESSION['admin_id']=1; hack to disable password request.
//echo '<pre>'.print_r($registry, TRUE).'</pre>';
//echo (intval($access_admin).' & '.intval($_SESSION['admin_rights']).' = '.($access_admin & $_SESSION['admin_rights']));

	$demo_mode = $_SESSION['admin_demo'];

	$message = '';
//echo '<pre>'.print_r($registry, TRUE).'</pre>';

	if ($auth_required == true) {
		if (intval($_SESSION['admin_id'])==0) {
			if ($_POST['action']=='login') {
				if ($_POST['login'].$_POST['password']) {
					if (LoginError::isBlocked()) {
						$message = 'Перевищено ліміт помилок входу.<br />Вас заблоковано на 30 хвилин.';
					}
					else {
						$rs = $db->query("SELECT * FROM `admin` WHERE `login`=".$db->escape($_POST['login'])." AND `password`=PASSWORD(".$db->escape($_POST['password']).")") or die('Get User: '.$db->lastError);
						if ($sa = $db->fetch($rs)) {
							$_SESSION['admin_id']     = $sa['id'];
							$_SESSION['admin_demo']   = $sa['rights']==0;
							$_SESSION['admin_rights'] = $sa['rights'];
							$db->query("UPDATE `admin` SET `last_login`=NOW() WHERE `id`={$sa['id']}");
							unset($_SESSION['login_error']);
//							header("Location: index.php"); // uncomment to disable pass-through authentification
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
			if (intval($_SESSION['admin_id'])==0 || $message!='') {
				$tpl = new template($registry->get('site_template_path').'login.htm', Template::SOURCE_FILE);
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
//echo (intval($access_admin).' & '.intval($_SESSION['admin_rights']).' = '.($access_admin & $_SESSION['admin_rights']));
		if ($access_admin && (! ($access_admin & $_SESSION['admin_rights']))) {
			header('HTTP/1.1 404 Not found', TRUE);
			header('Status: 404 Not found'/*, TRUE*/);
			echo 'Access denied: Page not found.';
			exit;
		}
	}
