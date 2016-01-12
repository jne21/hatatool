<?
$auth_required = true;
require('inc/authent.php');

use common\Page;
use common\TemplateFile as Template;
use system\FileList;
use CMS\RendererCMS as Renderer;
use CMS\I18n;

$ext = '.css';
$basePath = $registry->get('site_css_root');

$tpl = new Template($registry->get('cms_template_path').'file.htm');
$tpli = new Template($registry->get('cms_template_path').'file_item.htm');

$items = '';
foreach ($list = FileList::get($basePath.'*'.$ext) as $file) {
	$items .= $tpli->apply ([
			'name'  => $file['name'],
			'href'  => basename($file['name'], $ext),
			'group' => $file['group'],
			'owner' => $file['owner'],
			'perms' => $file['perms'],
			'size'  => $file['size'],
			'mtime' => $file['mtime'],
	        'extension' => substr($ext, 1)
	]);
}

$renderer = new Renderer(Page::MODE_NORMAL);

$i18n = new i18n($registry->get('cms_i18n_path').'file_editor.xml');

$pTitle = $i18n->get('title');

$renderer->page->set('title', $pTitle)
    ->set('h1', $pTitle)
    ->set('content',
        $tpl->apply([
                'items'     => $items,
                'site_root' => $registry->get('site_root')
        ])
    )
    ->renderProperty('h1', ['mask' => "*$ext"])
    ->renderProperty('title', ['mask' => "*$ext"]);

$renderer->loadPage();
$renderer->output();
