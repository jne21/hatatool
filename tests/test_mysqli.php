#!/usr/bin/php
<?php

require('../vendor/autoload.php');

use DB\Mysqli\Db as Db;

$db_settings = [
    Db::HOST => 'localhost',
    Db::LOGIN => 'testuser',
    Db::PASSWORD => 'testpassword',
    Db::DB_NAME => 'test'
];

$db = new Db($db_settings);

// $db->insert('test', ['name' => 'name5', 'description' => 'description5']);
// $db->update('test', ['name' => 'name 3', 'description' => 'description #3'], 'id=3');
// $db->delete('test', 5);
// $db->delete('test', 'name4', 'name');
/*
$recordset = $db->getRecordset('SELECT * FROM `test`');
while($record = $recordset->fetch()) {
//    var_dump($record);
    var_dump($record->asArray());
}
*/
$value = $db->getValue('SELECT * FROM `test`',2,2); var_dump($value);
