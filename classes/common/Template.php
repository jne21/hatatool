<?php

namespace common;

use common\Registry;

class Template {

	const
		SOURCE_VARIABLE = 2,
		SOURCE_DB       = 1,
		SOURCE_FILE     = 0,
		SOURCE_DEFAULT  = 1,

		DB_DEFAULT_TABLE       = 'template',
		DB_DEFAULT_FIELD_KEY   = 'alias',
		DB_DEFAULT_FIELD_VALUE = 'html';

	public $tpl;

	function __construct($source='', $mode=self::SOURCE_DEFAULT, $setup=NULL) {
		if ($source) {
			switch ($mode) {
				case self::SOURCE_VARIABLE: // from variable
					$this->open_variable($source);
					break;
				case self::SOURCE_FILE:
					$this->open_file($source);
					break;
				case self::SOURCE_DEFAULT:
				case self::SOURCE_DB: // from DB
					$this->open_db($source, $setup);
					break;
			}
		}
	}

	function open_file($fname) {
		$f = fopen($fname, 'r');
		if ($f==false) return (false);
		else {
			$this->tpl = fread($f, max(filesize($fname), 1)); // max - is the patch for null-sized files
			fclose($f);
			return (true);
		}
	}

	function open_db($alias, $setup) {
		if (!is_array($setup)) {
			$setup = array(
				'table'       => self::DB_DEFAULT_TABLE,
				'field_key'   => self::DB_DEFAULT_FIELD_KEY,
				'field_value' => self::DB_DEFAULT_FIELD_VALUE
			);
		}
		$registry = registry::getInstance();
		$db = $registry->get('db');
		$rs = $db->query("SELECT * FROM {$setup['table']} WHERE {$setup['field_key']}='$alias'") or die('template->open_db: '.$db->lastError);
		if ($sa = $db->fetch($rs)) {
			$this->tpl = $sa[$setup['field_value']];
		}
		else echo("template->open_db({$setup['table']}.{$setup['field_key']}='$alias') Alias not found.");
		$db->free($rs);
	}

	function open_variable($text) {
		$this->tpl = $text;
	}

	function apply($src=NULL) {
		$tpl = preg_replace('/<!---[\s\S]*--->/iU', "", $this->tpl); // remove comments
		if (is_array($src)) {
			foreach($src as $key => $val) {
				preg_match_all('/\{\?'.$key.'\}([\s\S]*)\{\/'.$key.'\}/iU', $tpl, $sources, PREG_SET_ORDER);
				foreach($sources as $source) {
					$args = preg_split('/\{:'.$key.'\}/iU', $source[1]);
					$tpl  = str_replace($source[0], $args[empty($val)], $tpl);
				}
				$tpl = str_replace('{'.$key.'}', $val, $tpl);
			}
		}
		return ($tpl);
	}
}
