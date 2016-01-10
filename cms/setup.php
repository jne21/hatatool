<?php
require('inc/authent.php');

use common\Setup;
use common\SetupItem;
use CMS\RendererCMS as Renderer;
use common\Page;
use common\TemplateFile as Template;

$i18n = new i18n($registry->get('cms_i18n_path') . 'admin.xml');

$tpl = new Template($registry->get('cms_template_path').'setup.htm');
$tpli = new Template($registry->get('cms_template_path').'setup_item.htm');

$items = '';
$setup = new Setup;
foreach ($setup->getList() as $variable) {
	$items .= $tpli->apply (
		array (
			'name'  => htmlspecialchars($variable->getProperty('name')),
			'value' => htmlspecialchars($variable->getProperty('value')),
			'desc'  => $variable->getProperty('description')
		)
	);
}

$renderer = new Renderer(Page::MODE_NORMAL);

$pTitle = $i18n->getText('title');
$renderer->page->set('title', $pTitle)
->set('h1', $pTitle)
->set('content',
        $tpl->apply(
                array(
                        'items' => $listItems,
                        'site_root' => $registry->get('site_root')
                )));

$renderer->loadPage();
$renderer->output();
	