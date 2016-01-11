<?php
$auth_required = true;
require('inc/authent.php');

use common\TemplateFile as Template;
use common\Redirect;
use common\Page;
use CMS\I18n;
use CMS\RendererCMS as Renderer;
use common\Registry;

$locale = $registry->getItem('locales', $registry->get('setup')->get('cms_locale'));
$i18n = new i18n($registry->get('cms_i18n_path').'redirect.xml');

$tpl  = new Template($registry->get('cms_template_path').'redirect.htm');
$tpli = new Template($registry->get('cms_template_path').'redirect_item.htm');

$list = Redirect::getList();
$listItems = '';
$expired = 0;
$timeDistance = mktime(0,0,0, date('n') - Redirect::EXPIRATION_MONTHES, date('j'), date('Y'));
foreach($list as $redirectId => $redirect) {
	if ($old = max($redirect->dateCreate, $redirect->dateRequest) <= $timeDistance) {
		$expired++;
	}
	$listItems .= $tpli->apply (
		array (
			'id'          => $redirect->id,
			'source'      => $redirect->source,
			'destination' => $redirect->destination,
			'active'      => $redirect->active,
			'status'      => $redirect->status,
			'dateRequest' => $redirect->dateRequest ? date($locale['dateFormat'], $redirect->dateRequest) : '',
			'dateCreate'  => date($locale['dateFormat'], $redirect->dateCreate),
			'old'         => $old
		)
	);
}


$renderer = new Renderer(Page::MODE_NORMAL);
	
$pTitle = $i18n->get('title');
$renderer->page->set('title', $pTitle)
	->set('h1', $pTitle)
	->set('content',
    $tpl->apply([
			'total'   => count($list),
			'expired' => $expired,
            'items' => $listItems,
            'site_root' => $registry->get('site_root')
    ])
);
	
$renderer->loadPage();
$renderer->output();
