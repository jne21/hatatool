<?
	use CMS\Admin;
	use CMS\TemplateFile as Template;

	require('inc/authent.php');

	$i18n = new i18n($registry->get('site_i18n_root').'admin.xml');

	$tpl   = new Template('tpl/admin.htm');
	$tpli  = new template('tpl/admin_item.htm');

	$list = Admin::getList();
	$listItems = '';
	foreach ($list as $item) {
		$listItems .= $tpli->apply (
			array (
				'id'             => $item->id,
				'name'           => $item->name,
				'email'          => $item->email,
				'state'          => $item->state,
				'login'          => $item->login,
				'description'    => $item->description,
				'rights'         => $item->rights
			)
		);
	}

	$page = new Page;
	$pTitle = $i18n->getText('title');
	$page->title   = $pTitle;
	$page->h1      = $pTitle;
	$page->content = $tpl->apply (
		array (
			'items'         => $listItems,
			'site_root'     => $registry->get('site_root')
		)
	);


	$renderer = new Renderer($page);
	$renderer->output();
?>