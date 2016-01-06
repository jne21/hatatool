<?php

namespace common;

use \common\Template;

class Renderer {

	public
		$page,
		$content,
		$template;

	function __construct($content, $page) {
		$this->page = $page;
		$this->content = $content;
		$this->updateContent([
			'content'  => $this->page->content,
			'metaTags' => $this->page->metaTags,
			'title'    => $this->page->metaTitle
		]);
		foreach ($this->page->headers as $header => $forced) {
			header($header, $forced);
		}
	}

	function output() {
		header('Content-Length: '.strlen($this->content));
		echo $this->content;
	}

	function updateContent($data) {
		$tpl = new Template($this->content);
		$this->content = $tpl->apply($data);
		return $this;
	}

}
