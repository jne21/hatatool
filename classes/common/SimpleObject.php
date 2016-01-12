<?php

namespace common;
use common\Registry;

abstract class SimpleObject {

    function __construct ($entityId = NULL)
    {
        $db = Registry::getInstance()->get(static::DB);
        if ($id = intval($entityId)) {
            $rs = $db->query("SELECT * FROM `".static::TABLE."` WHERE `id`=$id");
            if ($data = $db->fetch($rs)) {
                $this->loadDataFromArray($data);
            }
        }
    }

    abstract function loadDataFromArray($data);
    
    static function getList($sql=NULL)
    {
        $db = Registry::getInstance()->get(static::DB);
        $rs = $db->query($sql ? $sql : 'SELECT * FROM `'.static::TABLE.'` ORDER BY `name`');
        $list = [];
        while($data = $db->fetch($rs)) {
            $entity = new static();
            $entity->loadDataFromArray($data);
            $list[$entity->id] = $entity;
        };
        return $list;
    }

    static function delete($entityId) {
        if ($id = intval($entityId)) {
            $db = Registry::getInstance()->get('db');
            if (defined('static::ORDER_FIELD_NAME')) {
                $rs = $db->query("SELECT `".static::ORDER_FIELD_NAME."` FROM `".static::TABLE."` WHERE `id`=$id");
                if ($sa = $db->fetch($rs)) {
                    $db->query("DELETE FROM `".static::TABLE."` WHERE `id`=$id");
                    $db->update(
                        static::TABLE,
                        [
                            static::ORDER_FIELD_NAME => $db->makeForcedValue("`".static::ORDER_FIELD_NAME."`-1")
                        ],
					   "`".static::ORDER_FIELD_NAME."`>={$sa[static::ORDER_FIELD_NAME]}"
                    );
                }
            }
            else {
                $db->query("DELETE FROM `".static::TABLE."` WHERE `id`=$id");
            }
		}
	}
	
    /**
     * Standard setter
     * @param string $property Property name
     * @param string $value New property value
     * @return \common\Redirect
     */
    function set($property, $value) {
        $this->$property = $value;
        return $this;
    }
    
    /**
     * Standard getter
     * @param string $property Property name
     */
    function get($property)
    {
        return $this->$property;
    }
    
}