<?php

namespace common;

/**
 * @author jne
 */
class EmailQueue
{
    const HIGH = 0;
    const NORMAL = 10;
    const LOW = 20;

    const DB = 'db';
    const TABLE = 'email_queue';

    const PACKET_SIZE = 100;

    public $id, $mailerId, $priority, $letter;
    private $serialized;

    function __construct($letterId = null)
    {
        if ($id = intval($letterId)) {
            $db = Registry::getInstance()->get(self::DB);
            if ($record = $db->getRecord("SELECT * FROM `" . self::TABLE . "` WHERE `id`=$id")) {
                $this->load($record);
            }
        }
    }

    function load($data)
    {
        $this->id         = $data->id;
        $this->mailerId   = $data->mailer_id;
        $this->priority   = $data->priority;
        $this->serialized = $data->serialized;
        $this->letter     = unserialize($this->serialized);
    }

    function getList($count = self::PACKET_SIZE)
    {
        $list = [];
        $recordset = Registry::getInstance()->get(self::DB)->getRecordset("SELECT * FROM `" . self::TABLE . "` ORDER BY `priority` LIMIT $count");
        while ($record = $recordset->fetch()) {
            $entity = new self();
            $entity->load($record);
            $list[$entity->id] = $entity;
        }
        return $list;
    }

    function purgeMailer($mailerId)
    {
        if ($id = intval($mailerId)) {
            Registry::getInstance()->get(self::DB)->delete(self::TABLE, $id, 'mailer_id');
        }
    }

    function save()
    {
        $db = Registry::getInstance()->get(self::DB);
        $data = [
            'mailer_id'  => $this->mailerId,
            'priority'   => $this->priority,
            'serialized' => serialize($this->letter),
        ];
        if ($this->id) {
            $db->update(self::TABLE, $data, "`id`=" . $this->id);
        }
        else {
            $db->insert(self::TABLE, $data);
        }
    }

    static function enqueue($object, $priority=self::NORMAL, $mailerId=null)
    {
        $entity = new self();
        $entity->priority = $priority;
        $entity->mailerId = $mailerId;
        $entity->letter = $object;
        $entity->save();
    }

    static function delete($letterId)
    {
        if ($id = intval($letterId)) {
            Registry::getInstance()->get(self::DB)->delete(self::TABLE, $id);
        }
    }

    static function sendPacket($count = self::PACKET_SIZE) {
        $list = self::getList($count);
        foreach ($list as $entity) {
            if ($entity->send()) {
                self::delete($entity->id);
            }
        }
    }
}
