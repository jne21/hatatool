<?php
$auth_required = TRUE;
require('inc/authent.php');

use CMS\Module;
use CMS\I18n;
use common\TemplateFile as Template;

$i18n = new i18n($registry->get('cms_i18n_path').'module.xml');
$pTitle = $i18n->get('title');

$tpl  = new Template($registry->get('cms_template_path').'module.htm');
$tpli = new Template($registry->get('cms_template_path').'module_item.htm');

$list = Module::getList();
$cnt = count($list);
$listItems = '';
foreach ($list as $line) {
	$listItems .= $tpli->apply (
		array (
			'id'      => $line['id'],
			'name'    => $line['name'],
			'url'     => $line['url'],
			'path'    => $line['path'],
			'num'     => $line['num'],
			'is_up'   => $line['num'] > 1,
			'is_dn'   => $line['num'] < $cnt
		)
	);
}

$content = $tpl->apply(
		array(
			'items' => $items
		)
	);

	require('inc/_admin_bottom.php');
