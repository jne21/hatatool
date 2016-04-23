<?php

namespace common;

use common\Registry;

abstract class L10n
{
    const TABLE = 'l10n';

    public $id, $parentId;

    /** @var array Хранилище массива локализации */
    public $data;

    /**
     * Создание экземпляра объекта локализации
     * @param int $parentId Идентификатор родительской сущности 
     */
    function __construct($parentId = NULL)
    {
        if ($id = intval($parentId)) {
            $this->parentId = $id;
        }
        foreach (self::loadData(intval($id)) as $data) {
            $this->load($item['locale_id'], $data);
        }
    }

    abstract function load($localeId, $data);
    abstract function getLocales();

    /**
     * Загрузка из БД данных локализации заданной сущности
     * @param int $parentId Идентификатор родителя
     * @return array
     */
    protected static function loadData($parentId=NULL)
    {
        $result = [];
        if ($id = intval($parentId)) {
            $db = Registry::getInstance()->get(static::DB);
            $recordset = $db->getRecordset("SELECT * FROM `".static::TABLE."` WHERE `parent_id`=$id");
            while ($record = $recordset->fetch()) {
                $result[] = $record;
            }
        }
        return $result;
    }

    /**
     * Получение массива с данными локализации для группы сущностей
     * @param int[] $idList Массив идентификаторов родителей
     * @return array
     */
    static function getListByIds($idList)
    {
        $result = [];
        if (is_array($idList) && count($idList)) {
            $ids = array_map('intval', $idList);
            foreach($l = self::loadByParentIds($ids) as $parentId=>$l10nData) {
                $l10n = new static();
                $l10n->parentId = $parentId;
                foreach ($l10nData as $localeId=>$l10nItem) {
                    $l10n->load($localeId, $l10nItem);
                }
                $result[$parentId] = $l10n;
            }
        }
        return $result;
    }

    private static function loadByParentIds($idList)
    {
        $result = [];
        if (is_array($idList) && count($idList)) {
            $db = Registry::getInstance()->get(static::DB);
            $ids = array_map('intval', $idList);
            $sql = "SELECT * FROM `".static::TABLE."` WHERE `parent_id` IN (".implode(',', $ids).")";
            $recordset = $db->getRecordset($sql);
            while ($record = $recordset->fetch()) {
                $result[$record->parent_id][$record->locale_id] = $record;
            }
        }
        return $result;
    }

    /**
     * Сохранение данных в БД
     * @param array $data
     */
    function saveData($data)
    {
        $registry = Registry::getInstance();
        $db = $registry->get(static::DB);
        foreach (array_keys($this->getLocales()) as $localeId) {
            $db->replace (
                static::TABLE,
                array_merge(
                    [
                        'parent_id' => $this->parentId,
                        'locale_id' => $localeId
                    ],
                    $data[$localeId]
                )
            );
        }
    }

    /**
     * Получаем массив идентификаторов родителей по значению атрибута.
     * @param string $parentTable Имя таблицы БД
     * @param string $field Имя поля локализации
     * @param string $value Присваиваемое значение
     * @param string $locale Идентификатор локализации. По умолчанию системный язык.
     * @return integer[]
     */
    static function getParentIdsListByValue($field, $value, $locale=NULL)
    {
        $registry = Registry::getInstance();
        $db = $registry->get(static::DB);
        if (!$locale) {
            $locale = $registry()->get('i18n_language'); 
        }
        $result = [];
        return $db->getValues("SELECT `parent_id` FROM `".static::TABLE."` WHERE `".$db->realEscapeString($field)."`=".$db->escape($value));
    }

    /**
     * Проверка уникальности значения для поля.
     * @param string $parentTable Имя таблицы БД
     * @param string $field Имя поля локализации
     * @param string $value Проверяемое значение
     * @param string $locale Идентификатор локализации
     * @return boolean
     */
    static function checkValueOriginality($field, $value, $locale=NULL)
    {
        $registry = Registry::getInstance();
        $db = $registry->get(self::DB);
        if (!$locale) {
            $locale = $registry()->get('i18n_language'); 
        }
        return 0==$db->getValue("SELECT IFNULL(COUNT(*), 0) FROM `".static::TABLE."` WHERE `".$db->realEscapeString($field)."`=".$db->escape($value));
    }

    /**
     * Получение значения локализации заданного поля 
     * @param string $field Имя поля
     * @param string $locale Идентификатор локализации
     */
    function get($field, $locale = NULL)
    {
        if (!$locale) {
            $locale = Registry::getInstance()->get('i18n_language'); 
        }
        return $this->data[$locale][$field];
    }

    /**
     * Установка значения локализации заданного поля
     * @param string $field Имя поля
     * @param string $value Присваимое значение
     * @param string $locale Идентификатор локализации
     * @return object
     */
    function set($field, $value, $locale = NULL)
    {
        if (!$locale) {
            $locale = Registry::getInstance()->get('i18n_language'); 
        }
        $this->data[$locale][$field] = $value;
        return $this;
    }

    /**
     * Удаление локализации заданной сущности
     * @param string $parentTable Имя таблицы БД
     * @param int $parentId Идентификатор родителя
     */
    static function deleteAll($parentId)
    {
        $db = Registry::getInstance()->get(static::DB);
        $db->delete(static::TABLE, $parentId, 'parent_id');
    }
}
