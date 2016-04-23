<?php

namespace system;

use \common\Registry;

class LoginError
{
    const
        DB    = 'db',
        TABLE = 'login_error',
        LIMIT = 5,
        BLOCK_TIME = 30;

    static function register($login, $password)
    {
        $db = Registry::getInstance()->get('db');
        $db->insert(
            self::TABLE,
            [
                'login'    => $login,
                'password' => $password,
                'ip'       => $db::makeForcedValue("INET_ATON('{$_SERVER['REMOTE_ADDR']}')")
            ]
        );
    }

    static function isBlocked()
    {
        $db = Registry::getInstance()->get('db');
        $ip = $db->escape($_SERVER['REMOTE_ADDR']);
        return self::LIMIT <= $db->getValue(
            "SELECT IFNULL(COUNT(*), 0) AS `cnt` FROM `".self::TABLE."` WHERE `ip`=INET_ATON($ip) AND TIMESTAMPDIFF(MINUTE, `timestamp`, CURRENT_TIMESTAMP) < ".self::BLOCK_TIME
        );
    }
}
