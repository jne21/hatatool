<?php
$auth_required = TRUE;
require('inc/authent.php');

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

    $i18n = new i18n($registry->get('cms_i18n_path').'module.xml');
    $pTitle = $i18n->getText( $id ? 'update_mode' : 'append_mode' );


$tpl = new template($registry->get('cms_template_path').'module_edit.htm', Template::SOURCE_FILE);
$content = $tpl->apply(
	array(
		'id'    => $id,
		'name'  => $name,
		'url'   => $url,
		'path'  => $path
	)
);
