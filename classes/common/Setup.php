<?php
namespace common;

use common\SetupItem;

final class Setup {
	const
		TABLE = 'setup',
		DB    = 'db'
	;
	protected
		$table, $db, $values;

function __construct($table = self::TABLE, $db = self::DB) {
	$registry = Registry::getInstance();
	$this->db    = $registry->get($db);
	$this->table = $this->db->realEscapeString($table);
	$rs = $this->db->query("SELECT * FROM `$table` ORDER BY `name`") or die(__METHOD__.'::'.$this->db->lastError);
	while ($sa = $this->db->fetch($rs)) {
		$this->values[$sa['name']] = new SetupItem($this, $sa);
	}
}

function get($name) {
	if (isset($this->values[$name])) {
		return $this->values[$name]->getProperty('value');
	}
	else {
		return NULL;
	}
}

function getList() {
	return $this->values;
}

function updateValue($name, $value) {
    if (isset($this->values[$name])) {
        $this->values[$name]->setProperty('value', $value);
		$this->values[$name]->save();
	}
}

function save($name, $value, $description) {
	if (isset($this->values[$name])) {
		$this->values[$name]->setProperty('value', $value);
		$this->values[$name]->setProperty('description', $description);
		$this->values[$name]->save();
	}
	else {
		$this->values[$name] = new SetupItem(
			$this,
			array (
				'name'        => $name,
				'value'       => $value,
				'description' => $description
			)
		);
		$this->values[$name]->create();
	}
}

/**
 * Standard getter
 * @param string $name
 * @return mixed
 */
function getProperty($name) {
    return $this->$name;
}

}
