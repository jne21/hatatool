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
		$this->parentObject->$db->update(
			$this->parenObject->table,
			array(
				'value'       => $this->value,
				'description' => $this->description
			),
			"`name`=".$db->escape($this->name)
		);
		echo $value;
	}

	function create() {
		$this->parentObject->$db->insert(
			$this->parenObject->table,
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