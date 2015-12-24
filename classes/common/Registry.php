<?php
namespace common {
class Registry {
	protected $objects = array();

	function set($name, $object) {
		$this->objects[$name] = $object;
	}
	function setProperty($name, $property, $value) {
		$this->objects[$name]->$property = $value;
	}

	function get($name) {
		return $this->objects[$name];
	}
	function getItem($name, $index) {
		return $this->objects[$name][$index];
	}

	public static function getInstance() {
		static $me;
		return is_object($me) ? $me : $me = new Registry;
	}
}
}