<?php
	require_once('../inc/connect.php');

	use CMS\Admin;

out( "CMS\Admin" );

	$admin_description  = 'Admin description';
	$admin_email        = 'admin email';
	$admin_login        = 'admin_login';
	$admin_name         = 'admin_name';
	$admin_password     = 'admin_password';
	$admin_state        = Admin::BLOCKED;
	$admin_rights       = Admin::RIGHTS_DEFAULT;

	$admin = new Admin;
	$admin->description = $admin_description;
	$admin->email       = $admin_email;
	$admin->login       = $admin_login;
	$admin->name        = $admin_name;
	$admin->setNewPassword($admin_password);
	$admin->state       = $admin_state;
	$admin->rights      = $admin_rights;

out( "CMS\Admin->save() - create mode" );
	$admin->save();

	$adminId = $admin->id;

	unset($admin);

out( "CMS\Admin->__construct()" );
	$adm1n = new Admin($adminId);

	compare($adm1n->description, $admin_description, "Create Admin: Error saving admin->description.");
	compare($adm1n->email,    $admin_email,  "Create Admin: Error saving admin->email");
	compare($adm1n->login,    $admin_login,  "Create Admin: Error saving admin->login");
	compare($adm1n->name,     $admin_name,   "Create Admin: Error saving admin->name");
	compare($adm1n->password, Admin::passwordEncode($admin_password),  "Create Admin: Error saving admin->password");
	compare($adm1n->state,    $admin_state,  "Create Admin: Error saving admin->state");
	compare($adm1n->rights,   $admin_rights, "Create Admin: Error saving admin->rights");

	$adm1n_description  = 'Admin description 1';
	$adm1n_email        = 'admin email 1';
	$adm1n_login        = 'admin_login 1';
	$adm1n_name         = 'admin_name 1';
	$adm1n_password     = 'admin_password 1';
	$adm1n_state        = Admin::ACTIVE;
	$adm1n_rights       = Admin::RIGHTS_ALL;

	$adm1n->description = $adm1n_description;
	$adm1n->email       = $adm1n_email;
	$adm1n->login       = $adm1n_login;
	$adm1n->name        = $adm1n_name;
	$adm1n->password    = $adm1n->setNewPassword($adm1n_password);
	$adm1n->state       = $adm1n_state;
	$adm1n->rights      = $adm1n_rights;

out( "CMS\Admin->save() - update mode");
	$adm1n->save();
	unset($adm1n);

	$adm2n = new Admin($adminId);

	compare($adm2n->id,          $adminId,           "Update Admin: Error saving admin->id.");
	compare($adm2n->description, $adm1n_description, "Update Admin: Error saving admin->description.");
	compare($adm2n->email,       $adm1n_email,       "Update Admin: Error saving admin->email.");
	compare($adm2n->login,       $adm1n_login,       "Update Admin: Error saving admin->login.");
	compare($adm2n->name,        $adm1n_name,        "Update Admin: Error saving admin->name.");
	compare($adm2n->password,    Admin::passwordEncode($adm1n_password), "Update Admin: Error saving admin->password.");
	compare($adm2n->state,       $adm1n_state,       "Update Admin: Error saving admin->state.");
	compare($adm2n->rights,      $adm1n_rights,      "Update Admin: Error saving admin->rights.");

	unset($adm2n);

	$adm3n = Admin::getInstance($adm1n_login, $adm1n_password);
	if ($adm3n === FALSE) {
		out( "GetInstance Admin: Error getInstance({$adminId}) Not Found." , true);
	}
	elseif ($adm3n instanceof Admin) {
		compare($adm3n->id, $adminId, "GetInstance Admin: Error getInstance({$adminId}).");
	}
	else {
		out( "GetInstance Admin: Error getInstance({$adminId}) Wrong data type." , true);
	}

	unset($adm3n);

out( "CMS\Admin->getList()");
	$list = Admin::getList();
	if (is_array($list)) {
		if (count($list)) {
			if (array_key_exists($adminId, $list)) {
				$adm5n = $list[$adminId];
				if ($adm5n instanceof Admin) {
					compare($adm5n->id,          $adminId,           "Admin getList: Error getting admin->id");
					compare($adm5n->description, $adm1n_description, "Admin getList: Error getting admin->description");
					compare($adm5n->email,       $adm1n_email,       "Admin getList: Error getting admin->email");
					compare($adm5n->login,       $adm1n_login,       "Admin getList: Error getting admin->login");
					compare($adm5n->name,        $adm1n_name,        "Admin getList: Error getting admin->name");
					compare($adm5n->password,    Admin::passwordEncode($adm1n_password), "Admin getList: Error getting admin->password");
					compare($adm5n->state,       $adm1n_state,       "Admin getList: Error getting admin->state");
					compare($adm5n->rights,      $adm1n_rights,      "Admin getList: Error getting admin->rights");
				}
				unset($adm5n);
			}
			else {
				out( "List Admin: Existing Admin($adminId) not found by getList()" , true);
			}
		}
		else {
			out( "List Admin: getList() returned empty array on not empty DB. Existing Admin($adminId) not found" , true);
		}
	}
	else {
		out( "List Admin: getList() returned wrong data type" , true);
	}

out( "CMS\Admin->delete()");

	Admin::delete($adminId);
	$adm4n = new Admin($adminId);
	if ($adm4n->id) {
		out( "Delete Admin: Error delete({$adminId})" , true);
	}

	out(PHP_EOL."... passed".PHP_EOL);


function createAdmin() {
	$admin = new Admin;
	$admin->login = "unittest-".date('YmdHis');
	$admin->password = 'qwerty';
	$admin->state = 0;
	$admin->rights = 0;
	$admin->save();
	return $admin;
}
