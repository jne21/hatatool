<?php
namespace CMS;

interface iTemplateEditor {

	static function delete($templateID);

	function save();

	static function getList();
}