<?php

namespace CMS;

use \common\Registry;
use \common\TemplateFile;
use \common\Template;
use \common\Page;

class RendererCMS extends \common\Renderer {

	function __construct($pageMode) {
		$registry = Registry::getInstance();
		$page = new Page;
		$page->mode = $pageMode;
		switch ($pageMode) {
			case Page::MODE_POPUP:
				$templateFileName = 'popup.htm';
				break;
			case Page::MODE_NORMAL:
			default:
				$templateFileName = 'main.htm';
		}
		$tpl = new TemplateFile($registry->get('cms_template_path').$templateFileName);
		parent::__construct($tpl->getContent(), $page);

	}

	function output() {
		$registry = Registry::getInstance();
		// Render Globals
		$tplMainMenu = new TemplateFile($registry->get('cms_template_path').'main_menu.htm');
		$this->updateContent([
				'h1' => $this->page->h1,
		        'title' => $this->page->title,
		        'main_menu'      => $tplMainMenu->apply([
						'admin'=>TRUE,
						'operator'=>TRUE
					]),
			'year_now'       => date('Y'),
			'site_root'      => $registry->get('site_root')
		]);
		parent::output();
	}
}
