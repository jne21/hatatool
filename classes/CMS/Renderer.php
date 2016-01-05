<?php

namespace CMS;

use \common\Registry;
use \common\TemplateFile;
use \common\Template;

class Renderer {

	public
		$page,
		$content;

	function __construct($page) {
		$registry = Registry::getInstance();
		$this->page = $page;
		switch ($this->page->pageMode) {
			case Page::MODE_POPUP:
				$tpl = new TemplateFile($registry->get('cms_template_path').'popup.htm');
				break;
			case Page::MODE_NORMAL:
			default:
				$tpl = new TemplateFile($registry->get('cms_template_path').'main.htm');
		}
		$this->content = $tpl->getContent();

		$this->updateContent(
			array(
				'content'             => $this->page->content,
				'meta_tags'           => $this->page->metaTags,
				'title'               => $this->page->metaTitle,
				'h1'                  => $this->page->h1,
				'breadcrumbs'         => $this->page->breadcrumbs, //str_replace('|', '<span class="divider">&bull;</span>', $page->breadcrumbs),
				'has_left_column'     => $this->page->has_left_column,
				'left'                => $this->page->left
			)
		);
	}

	function output() {
		$registry = Registry::getInstance();
		// Render Globals
		$this->updateContent(
			array(
				'year_now'       => date('Y'),
				'site_root'      => $registry->get('site_root'),
				'site_root_main' => $registry->get('site_root_main')
			)
		);
		foreach ($this->page->headers as $header => $forced) {
			header($header, $forced);
		}
		header('Content-Length: '.strlen($this->content));
		echo $this->content;
	}

	function updateContent($data) {
		$tpl = new Template($this->content);
		$this->content = $tpl->apply($data);
	}

}
?>