<?php

namespace common;

use \common\Registry;
use \common\Template;

class Renderer {

	public
		$page,
		$content,
		$template;

	function __construct($content, $page = null) {
	    $this->content = $content;
	    if (isset($page)) {
		    $this->page = $page;
            $this->loadPage();
	    }
	}

	function loadPage() {
        $registry = Registry::getInstance();
	    $this->updateContent([
	            'content'  => $this->page->content,
	            'metaTags' => $this->page->metaTags,
	            'title'    => $this->page->metaTitle,
	            // render globals
	            'site_root' => $registry->get('site_root')
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
