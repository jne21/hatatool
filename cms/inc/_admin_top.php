<?
	require_once($site_class_path.'class.template.php');

	$tpl_page = new template('tpl/main.htm', Template::SOURCE_FILE);
	$tplm = new template('tpl/main_menu.htm', Template::SOURCE_FILE);
	$main_menu = $tplm->apply (
		array (
			'admin'    => $_SESSION['admin_rights'] & USER_ADMIN,
			'operator' => $_SESSION['admin_rights'] & USER_OPERATOR,
			'rights'   => $cms_user_type[$_SESSION['admin_rights']]
		)
	);
?>
