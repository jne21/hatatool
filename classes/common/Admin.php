<?php
namespace common;

use common\Password;
use common\Capability;
use common\Registry;

class Admin {
	public
		$id,
		$description,
		$email,
		$login,
		$name,
		$password,
		$state,
		$rights,
		$dateCreate,
		$dateLogin,
		$locale,
		$capability
	;

	protected
		$newPassword;

	const
		DB    = 'db',
		TABLE = 'admin',

		BLOCKED = 0,
		ACTIVE = 1,

		RIGHTS_DEFAULT = 0x0,
		RIGHTS_ALL     = 0xFF
	;

	const
		ITEMS_ON_PAGE = 50
	;

	function __construct($personId=NULL)
	{
		if ($id = intval($personId)) {
			$db = Registry::getInstance()->get(self::DB);
			$rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `id`=$id");
			if ($sa = $db->fetch($rs)) {
				$this->loadDataFromArray($sa);
			}
		}
		$this->capability = new Capability(self::TABLE, $this->id);
	}

	function loadDataFromArray($sa)
	{
		$this->id          = intval($sa['id']);
		$this->description = $sa['description'];
		$this->email       = $sa['email'];
		$this->login       = $sa['login'];
		$this->name        = $sa['name'];
		$this->password    = $sa['password'];
		$this->state       = intval($sa['state']);
		$this->rights      = intval($sa['rights']);
		$this->dateCreate  = strtotime($sa['date_create']);
		$this->dateLogin   = strtotime($sa['date_login']);
		$this->locale      = $sa['locale'];
	}

	function save()
	{
		$db = Registry::getInstance()->get(self::DB);

		$person = [
			'description' => $this->description,
			'email'       => $this->email,
			'login'       => $this->login,
			'name'        => $this->name,
			'state'       => $this->state,
			'rights'      => $this->rights,
			'locale'      => $this->locale
		];

		if ($this->id) {
			if ($this->newPassword) {
				$person['password'] = self::passwordEncode($this->newPassword);
			}
			$db->update(self::TABLE, $person, "`id`=".$this->id);
		}
		else {
			$this->dateCreate = time();
			$this->password = self::passwordEncode($this->newPassword);

			$person['date_create'] = date('Y-m-d H:i:s', $this->dateCreate);
			$person['password'] = $this->password;

			$rs = $db->insert(self::TABLE, $person) or die($db->lastError);
			$this->id = $db->insertId();
			$this->capability->setParentId($this->id);
		}
		$this->newPassword = '';
		$this->capability->save();
	}

	function setNewPassword($password)
	{
		$this->newPassword = $password;
	}

	static function getInstanceByCapability($name, $value)
	{
		$db = Registry::getInstance()->get(self::DB);
		$db->query(

"SELECT `id` FROM `".self::TABLE."` `A`
 INNER JOIN `".Capability::TABLE."` `C`
	ON `A`.`id`=`C`.`parent_id`
 WHERE `C`.`object`='".self::TABLE."' AND `C`.`name`=".$db->escape($name)." AND `C`.`value`=".$db->escape("$value")

		);

		if ($sa = $db->fetch($rs)) {
			return new Admin($sa['id']);
		}
		else {
			return FALSE;
		}
	}

	static function getList() {
		$db = Registry::getInstance()->get(self::DB);

		$result = [];
		$rs = $db->query("SELECT * FROM `".self::TABLE."` ORDER BY `name`");
		while ($sa = $db->fetch($rs)) {
			$admin = new Admin;
			$admin->loadDataFromArray($sa);
			$result[$admin->id] = $admin;
		}
		return $result;
	}

	static function delete($personId) {
		if ($id = intval($personId)) {
			Capability::removeAll(self::TABLE, $id, self::DB);
			$db = Registry::getInstance()->get(self::DB)->query("DELETE FROM `".self::TABLE."` WHERE `id`=$id LIMIT 1");
		}
	}

	static function getInstance($login, $password) {
		$db = Registry::getInstance()->get(self::DB);
		$sql = 
"SELECT `id` FROM `".self::TABLE."` WHERE `login`=".$db->escape("$login")." AND `password`='".self::passwordEncode($password)."'";
//die($sql);
		$rs = $db->query($sql);

		if ($sa = $db->fetch($rs)) {
			return new Admin($sa['id']);
		}
		else {
			return FALSE;
		}
	}

	function getProperty($propertyName) {
		return $this->$propertyName;
	}

	static function setProperty($personId, $propertyName, $value) {
		if ($id = intval($personId)) {
			$db = Registry::getInstance()->get(self::DB);
			$db->update(self::TABLE, array ($propertyName => $value), 'id='.$id);
		}
	}

	function getNewPassword() {
		return $this->newPassword;
	}

	static function passwordEncode($password) {
		return sha1($password);
	}

}
