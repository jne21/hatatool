<?php

$auth_required = TRUE;
require('inc/authent.php');

use common\TemplateFile as Template;
use common\Page;
use CMS\RendererCMS as Renderer;
use CMS\I18n;

$ext = '.css';
$path = $registry->get('site_css_root');

$name = $_GET['name'];

$fname = $path.$name.$ext;

if (file_exists($fname)) {

	$tpl = new Template($registry->get('cms_template_path').'file_edit.htm');

	if ($_POST['action'] == 'save') {
		$group = filegroup($fname);
		$owner = fileowner($fname);
		$perms = fileperms($fname);

		rename($fname, $fname.'~');
		file_put_contents($fname, $_POST['file_content']);
		@chgrp($fname, $group);
		@chown($fname, $owner);
		@chmod($fname, $perms);

		if ($_POST["return"]=='true') {
			header("Location: css_edit.php?name=$name&saved=true");
		}
		else {
			header('Location: css.php');
		}
		exit;
	}
	else {

        $i18n = new i18n($registry->get('cms_i18n_path').'file_editor.xml');

        $renderer = new Renderer(Page::MODE_NORMAL);
        $pTitle = $i18n->get('editor_title');

        $renderer->page->set('title', $pTitle)
            ->set('h1', $pTitle)
            ->set(
                    'content',
                    $tpl->apply([
				        'name'         => $name,
				        'file_content' => file_get_contents($fname),
                        'site_root' => $registry->get('site_root')
                    ])
            )
            ->renderProperty('h1', ['fileName'=>$name.$ext])
            ->renderProperty('title', ['fileName'=>$name.$ext]);
        
        $renderer->loadPage();
        $renderer->output();
	}
}
else {
	header('Location: css.php');
}
