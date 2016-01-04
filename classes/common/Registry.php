<?php
namespace common {

/**
 * Simple memory cache singleton
 */
class Registry {
	/**
	 * Main storage
	 * @var array
	 */
	protected $objects = array();

	/**
	 * Prevents the creation of entities
	 */
	private function __construct() {
	}

	/**
	 * Get cache singleton instance
	 * @return Registry Cache object
	 */
	public static function getInstance() {
		static $me;
		return is_object($me) ? $me : $me = new Registry;
	}

	/**
	 * Store value in cache
	 * @param string $name Identifier
	 * @param mixed @object Value to save
	 */
	function set($name, $object) {
		$this->objects[$name] = $object;
	}

	/**
	 * Store value of some object's property in cache
	 * @param string $name Identifier
	 * @param string $property Property name
	 * @param mixed $value Value to save
	 */
	function setProperty($name, $property, $value) {
		$this->objects[$name]->$property = $value;
	}

	/**
	 * Retrieve value from cache
	 * @param string $name Identifier
	 * @return mixed Stored value or NULL if not found
	 */
	function get($name) {
		return $this->objects[$name];
	}

	/**
	 * Retrieve value of array element
	 * @param string $name Identifier
	 * @param mixed $index Array index
	 * @return mixed
	 */
	function getItem($name, $index) {
		return $this->objects[$name][$index];
	}

}
}