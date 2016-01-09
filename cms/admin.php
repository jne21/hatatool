<?
use CMS\Admin;
use common\TemplateFile as Template;
use common\I18n;
use common\Page;
use CMS\RendererCMS as Renderer;

require ('inc/authent.php');

$i18n = new i18n($registry->get('cms_i18n_path') . 'admin.xml');

$tpl = new Template($registry->get('cms_template_path') . 'admin.htm');
$tpli = new template($registry->get('cms_template_path') . 'admin_item.htm');

$listItems = '';

foreach (Admin::getList() as $item) {
    $listItems .= $tpli->apply(
            array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'state' => $item->state,
                    'login' => $item->login,
                    'description' => $item->description,
                    'rights' => $item->rights
            ));
}

$renderer = new Renderer(Page::MODE_NORMAL);

$pTitle = $i18n->getText('title');

$renderer->page->title = $pTitle;
$renderer->page->h1 = $pTitle;
$renderer->page->content = $tpl->apply(
        array(
                'items' => $listItems,
                'site_root' => $registry->get('site_root')
        ));
$renderer->loadPage();
$renderer->output();
?>