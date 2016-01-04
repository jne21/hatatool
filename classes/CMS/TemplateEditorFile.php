<?php

namespace CMS;

use common\Registry;

class TemplateEditorFile implements iTemplateEditor {

	public $fileName, $html;
	protected $templatePath;

	const
		EXT = 'htm';
	
	/**
	 * Создание экземпляра объекта из файла.
	 * @param string $fileName имя файла шаблона
	 **/
	function __construct($fileName) {
		$fileSpec = self::getTemplatePath($fileName);
		if (file_exists($fileSpec)) {
			$this->fileName = $fileName;
			$this->html     = file_get_contents($fileSpec);
		}
	}

	function save($createBackup = FALSE) {
		$fileSpec = self::getTemplatePath($self->fileName);
		@rename($fileSpec, $fileSpec.'~');
		file_put_contents($fileSpec, $this->html);
	}

	static function delete($fileName) {
		@unlink(self::getTemplatePath($fileName));
	}
	
	static function getList() {
		$list = [];
		foreach (glob(self::getTemplatePath('*.'.self::EXT)) as $fileName) {
    		$list[] =  $fileName; //filesize($filename);
		}
		return $list;
	}

	static function getTemplatePath($fileName) {
		return Registry::getInstance()->get('site_template_path').$fileName;
	}
}