<?php

namespace common;

use common\RedirectQuery;

class Redirect extends SimpleObject {

    const
        EXPIRATION_MONTHES = 6,
        DB = 'db',
        TABLE = 'redirect',
        ORDER_FIELD_NAME = 'order',
        ACTIVE = 1
    ;

    public
        $id,
        $source,
        $destination,
        $active,
        $status,
        $dateRequest,
        $dateCreate,
        $order;
    
    
    use \common\entity;
    
    /**
     * Загрузка данных в объект из внешнего массива.
     * @param array $data
     */
    function loadDataFromArray($data) {
        $this->id          = $data['id'];
        $this->source      = $data['source'];
        $this->destination = $data['destination'];
        $this->active      = $data['active'];
        $this->status      = $data['status'];
        $this->dateRequest = strtotime($data['date_request']);
        $this->dateCreate  = strtotime($data['date_create']);
        $this->order       = $data[self::ORDER_FIELD_NAME];
    }

    /**
     * Получение списка редиректов в виде массива объектов
     * @return multitype:\common\Redirect[]
     */
    static function getList($mode = NULL) {
        return parent::getList("SELECT * FROM `".self::TABLE."`".($mode==self::ACTIVE ? ' WHERE `active`='.self::ACTIVE : '')." ORDER BY `".self::ORDER_FIELD_NAME."`");
    }

    /**
     * Сохранение объекта в базе данных
     */
    function save() {
        $db = Registry::getInstance()->get('db');
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
            $record['date_create'] = $db->makeForcedValue('NOW()');
            $record[self::ORDER_FIELD_NAME] = 0;
            $db->insert(self::TABLE, $record) or die($db->lastError);
            $db->update(
                    self::TABLE,
                    ['num' => $db->makeForcedValue('`num`+1')],
                    ''
            );
        }
        
    }

    /**
     * Обновление даты последнего запроса
     * @param int $entityId
     */
    static function updateRequestDate($entityId) {
        if ($id = intval($entityId)) {
            self::updateValue($id, 'date_request', date('Y-m-d H:i:s'));
        }
    }

    /**
     * Удаление истории запросов для заданного редиректа
     * @param int $entityId Идентификатор редиректа
     */
    static function purge($entityId) {
        RedirectQuery::purge($entityId);
        self::updateValue($entityId, 'date_request', NULL);
    }

}