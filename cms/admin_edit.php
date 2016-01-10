<?php
use CMS\Admin;
use CMS\RendererCMS as Renderer;
use common\Page;
use common\Registry;
use common\I18n;
use common\TemplateFile as Template;

require('inc/authent.php');

$admin = new Admin($id = intval($_GET['id']));

if ($_POST['action'] == 'save') { //d($_POST, 1);
	$admin->description  = trim($_POST['description']);
	$admin->email        = trim($_POST['email']);
	$admin->login        = trim($_POST['login']);
	$admin->name         = trim($_POST['name']);
    $admin->rights       = intval($_POST['rights']);
    $admin->state        = intval($_POST['state']=='on');
	if ($_POST['password']) {
        $admin->setNewPassword($_POST['password']);
	}
    $admin->save();
    header('Location: admin.php');
    exit;
}
else {
    $i18n = new I18n($registry->get('cms_i18n_path').'admin.xml');
    $locale = $registry->getItem('locales', $registry->get('cms_locale'));
    $tpl = new Template($registry->get('cms_template_path').'admin_edit.htm');

    $renderer = new Renderer(Page::MODE_NORMAL);
    
    $pTitle = $i18n->getText(
        $admin->id ?  'update_mode' : 'append_mode'
    );
    $renderer->page->set('title', $pTitle)
        ->set('h1', $pTitle)
        ->set('content',
            $tpl->apply(
                    array(
                            'id' => $admin->id,
                            'description' => htmlspecialchars($admin->description),
                            'email' => htmlspecialchars($admin->email),
                            'login' => htmlspecialchars($admin->login),
                            'name' => htmlspecialchars($admin->name),
                            'rights' => $admin->rights,
                            'state' => $admin->state,
                            'dateCreate' => date($locale['dateFormat'], $admin->dateCreate),
                            'dateLogin' => $admin->dateLogin ? date($locale['dateFormat'] . ' H:i', $admin->dateLogin) : '',
                            'site_root' => $registry->get('site_root')
                    )));
    
    $renderer->loadPage();
    $renderer->output();
}