<?php
namespace common;

class I18n
{
    public $language;
    private $dom, $root;
//	public $dom, $root;

    function __construct($xml)
    {
        $this->dom = new \DOMDocument;
        $this->dom->validateOnParse = TRUE;
        if ($this->dom->load($xml)) {
            $this->root = $this->dom->documentElement;
        }
        $this->language = Registry::getInstance()->get('i18n_language');
    }

    function get($key, $lang='')
    {
        if (empty($lang)) $lang=$this->language;
        if (($node = $this->dom->getElementById($key)) === NULL) {
            $value = "<strong>[$key]</strong>[$lang]"; // key not found
        }
        else {
            $lnode = $node->getElementsByTagName($lang);
            if ($lnode->item(0) === NULL) {
                $value = "[$key]<strong>[$lang]</strong>"; // no i18n for selected key
            }
            else {
                $value = "[$key][$lang]Empty"; // ok
                foreach ($lnode->item(0)->childNodes as $cnode) {
                    $nodeClass = get_class($cnode);
                    if ($nodeClass == 'DOMText' || $nodeClass == 'DOMCdataSection') {
                        $value = $cnode->wholeText;
                        break;
                    }
                }
            }
        }
        return $value;
    }
}
