<?php

namespace common;
use common\Registry;

abstract class SimpleObject
{
    function __construct ($entityId = NULL)
    {
        $db = Registry::getInstance()->get(static::DB);
        if ($id = intval($entityId)) {
            if ($record = $db->getRecord("SELECT * FROM `".static::TABLE."` WHERE `id`=$id")) {
                $this->load($record);
            }
        }
    }

    abstract function load($data);
    
    static function getList($sql=NULL)
    {
        $db = Registry::getInstance()->get(static::DB);
        $recordset = $db->getRecordset($sql ? $sql : 'SELECT * FROM `'.static::TABLE.'` ORDER BY `name`');
        $list = [];
        while($record = $recordset->fetch()) {
            $entity = new static();
            $entity->load($record);
            $list[$entity->id] = $entity;
        }
        return $list;
    }

    static function delete($entityId)
    {
        if ($id = intval($entityId)) {
            $db = Registry::getInstance()->get('db');
            if (defined('static::ORDER_FIELD_NAME')) {
                if ($record = $db->getRecord("SELECT `".static::ORDER_FIELD_NAME."` FROM `".static::TABLE."` WHERE `id`=$id")) {
                    $db->delete(static::TABLE, $id);
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
                $db->delete(static::TABLE, $id);
            }
        }
    }
	
    /**
     * Standard setter
     * @param string $property Property name
     * @param string $value New property value
     * @return \common\Redirect
     */
    function set($property, $value)
    {
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
