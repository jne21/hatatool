<?php
$auth_required = TRUE;
require('inc/authent.php');

use common\Page;
use CMS\RendererCMS as Renderer;
use CMS\I18n;

$i18n = new I18n($registry->get('cms_i18n_path') . 'index.xml');
$renderer = new Renderer(Page::MODE_NORMAL);

$pTitle = $i18n->getText('title');
$renderer->page->set('title', $pTitle)
    ->set('h1', $pTitle)
    ->set('content', '');

$renderer->loadPage();
$renderer->output();

