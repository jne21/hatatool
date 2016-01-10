<?php
namespace common;

final class SetupItem {
	protected
		$name, $value, $description, $parentObject;

	function __construct(Setup $parentObject, $data) {
		$this->parentObject = $parentObject;
		$this->name         = $data['name'];
		$this->value        = $data['value'];
		$this->description  = $data['description'];
	}

	function save() {
	    $db = $this->parentObject->getProperty('db'); 
	    $db->update(
			$this->parentObject->getProperty('table'),
			array(
				'value'       => $this->value,
				'description' => $this->description
			),
			"`name`=".$db->escape($this->name)
		) or die($db->lastError);
	}

	function create() {
		$this->parentObject->getProperty('db')->insert(
			$this->parentObject->getProperty('table'),
			array(
				'name'        => $this->name,
				'value'       => $this->value,
				'description' => $this->description
			)
		);
	}

	function getProperty($name) {
		if (isset($this->$name)) {
			return $this->$name;
		}
		else {
			return NULL;
		}
	}

	function setProperty($name, $value) {
		$this->$name = $value;
	}
}