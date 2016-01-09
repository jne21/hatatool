<?
use CMS\Admin;
use common\TemplateFile as Template;
use common\I18n;
use common\Page;
use CMS\RendererCMS as Renderer;

require ('inc/authent.php');

$i18n = new i18n($registry->get('cms_i18n_path') . 'admin.xml');
$locale = $registry->getItem('locales', $registry->get('cms_locale'));

$tpl = new Template($registry->get('cms_template_path') . 'admin.htm');
$tpli = new template($registry->get('cms_template_path') . 'admin_item.htm');

$listItems = '';

foreach (Admin::getList() as $item) {
    $listItems .= $tpli->apply(
            array(
                    'id' => $item->id,
                    'description' => $item->description,
                    'email' => $item->email,
                    'login' => $item->login,
                    'name' => $item->name,
                    'state' => $item->state,
                    'rights' => $item->rights,
                    'dateCreate' => date($locale['dateFormat'], $item->dateCreate),
                    'dateLogin' => ($item->dateLogin ? date($locale['dateFormat'], $item->dateLogin) : '')
            ));
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
?>