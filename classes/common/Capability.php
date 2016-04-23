<?php

namespace common;
/**
 * Объект доступа к дополнительным свойствам объектов.
 */
final class Capability
{
    /**
     * Название (идентификатор) свойства
     */
    protected $capability = [];

    /**
     * Название объекта (как правило, таблицы БД)
     */
    protected $object;

    /**
     * Идентификатор объекта - владельца свойства
     */
    protected $parentId;

    const
        /**
         * Таблица БД для хранения свойств
         */
        TABLE = 'capability',

        /**
         * БД для хранения свойств в терминах системного реестра
         */
        DEFAULT_DB = 'db'
    ;

    protected
        /**
         * Текущая БД
         */
        $db;

    /**
     * Конструктор объекта.
     * @param string $object название объекта
     * @param string $parentId идентификатор родителя
     * @param string $dbName имя бд по системному реестру (опционально)
     */
    function __construct($object, $parentId, $dbName=self::DEFAULT_DB)
    {
        $this->db = Registry::getInstance()->get($dbName);
        $this->object = $object;
        $this->parentId = $parentId;
        $recordset = $this->db->getRecordset(

"SELECT * FROM `".self::TABLE."` WHERE `object`=".$this->db->escape($this->object)." AND `object_id`=".$this->db->escape($this->parentId)

        ) or die(__METHOD__.': '.$this->db->lastError);
        while($record = $recordset->fetch()) {
            $this->set($record->name, $record->value);
        }
    }

    /**
     * Возвращает значение свойства по имени.
     * @param string $capabilityName название свойства
     * @return general значение свойства
     */
    function get($capabilityName)
    {
        return $this->capability[$capabilityName];
    }

    /**
     * Возвращает TRUE, если свойство установлено, и FALSE в противном случае.
     * @param string $capabilityName название свойства
     * @return boolean результат поиска
     */
    function exists($capabilityName)
    {
        return isset($this->capability[$capabilityName]);
    }

    /**
     * Возвращает полный список свойств.
     * не реализовано
     * @return string имя свойства
     */
    function getAll($filter=NULL)
    {
        return $this->capability;
    }

    /**
     * Сохраняет установленные свойства в БД.
     */
    function save()
    {
        self::removeAll($this->object, $this->parentId);
        foreach($this->capability as $name=>$value) {
            $this->db->insert(
                self::TABLE,
                [
                    'object'    => $this->object,
                    'object_id' => $this->parentId,
                    'name'      => $name,
                    'value'     => $value
                ]
            );
        }
    }

    /**
     * Удаляет значение свойства по имени.
     * @param string $capabilityName название свойства
     */
    function remove($capabilityName)
    {
        unset($this->capability[$capabilityName]);
    }

    /**
     * Устанавливает значение свойства по имени.
     * @param string $capabilityName название свойства
     * @param string value значение свойства
     */
    function set($capabilityName, $value)
    {
        $this->capability[$capabilityName] = $value;
    }

    /**
     * Устанавливает значение идентификатора родительского объекта для нового свойства
     * @param integer $parentId идентификатор объекта
     */
    function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Удаляет свойства по заданному значению.
     * @param string $object название объекта
     * @param string $name название свойства
     * @param string $value значение свойства
     * @param string $database название БД в терминах системного реестра
     */
    static function removeByContent($object, $name, $value, $database=self::DEFAULT_DB)
    {
        $db = Registry::getInstance()->get($database);
        $db->query (

"DELETE FROM `".self::TABLE."` WHERE `object`=".$db->escape($object)." AND `name`=".$db->escape("$name")." AND `value`=".$db->escape("$value")

        ) or die(__METHOD__.': '.$db->lastError);
    }

    /**
     * Удаляет все свойства заданного объекта.
     * @param string $object название объекта
     * @param string $parentId идентификатор объекта
     * @param string $database название БД в терминах системного реестра
     */
    static function removeAll($object, $parentId, $database = self::DEFAULT_DB)
    {
        $db = Registry::getInstance()->get($database);
        $db->query (

"DELETE FROM `".self::TABLE."` WHERE `object`=".$db->escape($object)." AND `object_id`=".$db->escape($parentId)

        ) or die(__METHOD__.': '.$db->lastError);
    }
}
