<?php

namespace CMS;

use common\Registry;

class TemplateFile extends Template {

	/**
	 * Создание экземпляра класса из файла
	 * @param string $fileSpec полная спецификация имени файла.
	 */
	function __construct($fileSpec) {
		$this->tpl = file_get_contents($filespec);
	}
}
