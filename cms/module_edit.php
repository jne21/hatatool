<?php
$auth_required = TRUE;
require('inc/authent.php');

use common\Page;
use common\TemplateFile as Template;
use CMS\RendererCMS as Renderer;
use CMS\Module;
use CMS\I18n;

$module = new Module(intval($_GET['id']));

if ($_POST['action']=='save') {
	$module->name  = trim($_POST['name']);
	$module->url   = trim($_POST['url']);
	$module->path  = trim($_POST['path']);
	$module->save();

	header('Location: module.php');
	exit;
}
else {

    $i18n = new I18n($registry->get('cms_i18n_path').'module.xml');
    $pTitle = $i18n->get( $id ? 'update_mode' : 'append_mode' );
    
    $tpl = new Template($registry->get('cms_template_path').'module_edit.htm');

    $renderer = new Renderer(Page::MODE_NORMAL);
    $renderer->page->set('title', $pTitle)
        ->set('h1', $pTitle)
        ->set('content',
            $tpl->apply([
                    'id'    => $module->id,
                    'name'  => htmlspecialchars($module->name),
                    'url'   => htmlspecialchars($module->url),
                    'path'  => $module->path,

                    'site_root' => $registry->get('site_root')
                ]));

    $renderer->loadPage();
    $renderer->output();
}
