<?
$auth_required = true;
require('inc/authent.php');

use common\Redirect;
use common\RedirectQuery;
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
    $locale = $registry->getItem('locales', $registry->get('setup')->get('cms_locale'));
    
    $queries = '';
    if ($redirect->id) {
	    $tplq = new Template($registry->get('cms_template_path').'redirect_query_item.htm');
        foreach (RedirectQuery::getList($redirect->id) as $query) {
            $queries .= $tplq->apply ([
				'date'                  => date($locale['dateFormat'].'&\nb\sp;H:i', $query->date),
				'HTTP_REFERER'          => $query->HTTP_REFERER,
				'REMOTE_ADDR'           => $query->REMOTE_ADDR,
				'HTTP_USER_AGENT'       => $query->HTTP_USER_AGENT,
				'REDIRECT_URL'          => $query->REDIRECT_URL,
				'REDIRECT_QUERY_STRING' => $query->REDIRECT_QUERY_STRING
			]);
        }
	}

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
