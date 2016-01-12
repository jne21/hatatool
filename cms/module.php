<?php
$auth_required = TRUE;
require('inc/authent.php');

use common\TemplateFile as Template;
use common\Page;
use CMS\Module;
use CMS\I18n;
use CMS\RendererCMS as Renderer; 

$i18n = new i18n($registry->get('cms_i18n_path').'module.xml');

$tpl  = new Template($registry->get('cms_template_path').'module.htm');
$tpli = new Template($registry->get('cms_template_path').'module_item.htm');

$list = Module::getList();
$cnt = count($list);
$listItems = '';
foreach ($list as $line) {
	$listItems .= $tpli->apply (
		array (
			'id'      => $line->id,
			'name'    => $line->name,
			'url'     => $line->url,
			'path'    => $line->path
		)
	);
}

$renderer = new Renderer(Page::MODE_NORMAL);

$pTitle = $i18n->get('title');
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
