<?php
namespace CMS;

interface iTemplateEditor {

	public abstract function save();
	public abstract static function delete($templateID);
	public abstract static function getList();

}