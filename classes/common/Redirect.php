<?php

namespace common;

class Redirect {

    const
        EXPIRATION_MONTHES = 6,
        DB = 'db',
        TABLE = 'redirect',
        ORDER_FIELD_NAME = 'order'        
    ;

    public $id, $source, $destination, $active, $status, $dateRequest, $dateCreate, $order;
    
    
    use \common\entity;
    
    function __construct($instanceId = NULL) {
        if ($id = intval($instanceId)) {
            $db = Registry::getInstance()->get('db');
            $rs = $db->query("SELECT * FROM `".self::TABLE."` WHERE `id`=$id");
            if ($sa = $db->fetch($rs)) {
                $this->loadDataFromArray($sa);
            }
        }
    }

    function loadDataFromArray($data) {
        $this->id          = $data['id'];
        $this->source      = $data['source'];
        $this->destination = $data['destination'];
        $this->active      = $data['active'];
        $this->status      = $data['status'];
        $this->dateRequest = strtotime($sa['date_request']);
        $this->dateCreate  = strtotime($sa['dateCreate']);
        $this->order       = $data[self::ORDER_FIELD_NAME];
    }

    static function getList() {
        $list = [];
        $db = Registry::getInstance()->get('db');
        $rs = $db->query("SELECT * FROM `".self::TABLE."` ORDER BY `".self::ORDER_FIELD_NAME."`");
        while($data = $db->fetch($rs)) {
            $entity = new Redirect();
            $entity->loadDataFromArray($data);
            $list[$entity->id] = $entity;
        }
        return $list;
    }

    function save() {
        $record = [
                'source'      => $this->source,
                'destination' => $this->destination,
                'active'      => $this->active,
                'status'      => $this->status
        ];
        if ($this->id) {
            $db->update(self::TABLE, $record, "`id`=".$this->id);
        }
        else {
            $record['dateCreate'] = $db->makeForcedValue('NOW()');
            $record[self::ORDER_FIELD_NAME] = 0;
            $db->insert(self::TABLE, $record);
            $db->update(
                    self::TABLE,
                    ['num' => $db->makeForcedValue('`num`+1')],
                    ''
            );
        }
        
    }

    static function updateRequestDate($entityId) {
        if ($id = intval($entityId)) {
            self::updateValue($id, 'date_request', date('Y-m-d H:i:s'));
        }
    }
    
    static function delete($id) {
    	if ($id = intval($_GET['id'])) {
            $db = Registry::getInstance()->get('db');
    	    $rs = $db->query("SELECT `".self::ORDER_FIELD_NAME."` FROM `".self::TABLE."` WHERE `id`=$id");
            if ($sa = $db->fetch($rs)) {
                $db->query("DELETE FROM `".self::TABLE."` WHERE `id`=$id");
			    $db->update(
			            self::TABLE,
			            [
			                    self::ORDER_FIELD_NAME => $db::makeForcedValue('`'.self::ORDER_FIELD_NAME.'`-1')
                        ],
			            "`".self::ORDER_FIELD_NAME."`>={$sa[self::ORDER_FIELD_NAME]}"
			    );
		    }
        }
    }

    function set($property, $value) {
        $this->$property = $value;
        return $this;
    }
    
    function get($property) {
        return $this->$property;
    }

}