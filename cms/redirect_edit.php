<?
$auth_required = true;
require('inc/authent.php');

use common\Redirect;
use common\Page;
use common\TemplateFile as Template;
use CMS\I18n;
use CMS\RendererCMS as Renderer;

$redirect = new Redirect(intval($_GET['id']));

if ($_POST['action']=='save') {

	$redirect->source      = trim($_POST['source']);
	$redirect->destination = trim($_POST['destination']);
	$redirect->active      = intval($_POST['active']=='on');
	$redirect->status      = intval($_POST['status']);
	$redirect->save();

	header('Location: redirect.php');
	exit;
}
else {

    $i18n = new I18n($registry->get('cms_i18n_path').'redirect.xml');
/*
    $queries = '';
    if ($id) {
	$tplq = new Template('tpl/redirect_query_item.htm', Template::SOURCE_FILE);

	$rsq = $db->query("SELECT * FROM `redirect_query` WHERE `redirect_id`=$id ORDER BY `dt` DESC") or die('Redirect Query: '.$db->lastError);
	while ($saq = $db->fetch($rsq)) {
		$queries .= $tplq->apply (
			array (
				'dt'                    => date('d.m.y&\nb\sp;H:i', strtotime($saq['dt'])),
				'HTTP_REFERER'          => $saq['HTTP_REFERER'],
				'REMOTE_ADDR'           => $saq['REMOTE_ADDR'],
				'HTTP_USER_AGENT'       => $saq['HTTP_USER_AGENT'],
				'REDIRECT_URL'          => $saq['REDIRECT_URL'],
				'REDIRECT_QUERY_STRING' => $saq['REDIRECT_QUERY_STRING']
			)
		);
	}
*/
    

    $tpl = new Template($registry->get('cms_template_path').'redirect_edit.htm');

    $renderer = new Renderer(Page::MODE_NORMAL);

    $pTitle = $i18n->get( $id ? 'update_mode' : 'append_mode' );
    $renderer->page->set('title', $pTitle)
        ->set('h1', $pTitle)
        ->set('content',
        $tpl->apply([
            'id'          => $redirect->id,
            'source'      => $redirect->source,
            'destination' => $redirect->destination,
            'active'      => $redirect->id ? $redirect->active : TRUE,
    		'status'      => $redirect->id ? $redirect->status : 301,
            'queries'     => $queries,
            'site_root'   => $registry->get('site_root')
        ])
    );

    $renderer->loadPage();
    $renderer->output();


}
