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
        else {
            $this->page = new Page();
        }
    }

    function loadPage() {
        $this->updateContent([
            'content'  => $this->page->get('content'),
            'metaTags' => $this->page->get('metaTags'),
            'title'    => $this->page->get('metaTitle')
        ]);
    }

    function output() {
        foreach ($this->page->get('headers') as $header => $forced) {
            header($header, $forced);
        }
        header('Content-Length: '.strlen($this->content));
        echo $this->content;
    }

    function updateContent($data) {
        $tpl = new Template($this->content);
        $this->content = $tpl->apply($data);
        return $this;
    }

}
