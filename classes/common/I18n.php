<?php
namespace common;

class I18n {
	public $language;
	private $dom, $root;
//	public $dom, $root;

	function __construct($xml) {

		$this->dom = new \DOMDocument;
		$this->dom->validateOnParse = TRUE;
		if ($this->dom->load($xml)) {
			$this->root = $this->dom->documentElement;
		}
		$this->language = Registry::getInstance()->get('i18n_language');
	}

	function getText($key, $lang='') {
		if (empty($lang)) $lang=$this->language;
		if (($node = $this->dom->getElementById($key)) === NULL) {
			$value = "<strong>[$key]</strong>[$lang]"; // Не существует ключ
		}
		else {
			$lnode = $node->getElementsByTagName($lang);
			if ($lnode->item(0) === NULL) {
				$value = "[$key]<strong>[$lang]</strong>"; // Не определены значения для ключа
			}
			else {
				$value = "[$key][$lang]Empty"; // Нет значения ключа на запрошенном языке
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
