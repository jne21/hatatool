<?php

namespace DB;

abstract class AbstractRecordset
{
    private $db;
    private $recordset;
    
    function __construct($db, $recordset)
    {
        $this->db = $db;
        $this->recordset = $recordset;
    }

    /**
     * Возвращает количество строк
     * @return int
     */
    abstract function numRows();

    /**
     * Возвращает значение из результата запроса.
     * @param number $row номер строки (начиная с 0)
     * @param number|string $field номер или имя поля в строке
     * @param unknown $default значение по умолчанию, если заданное поле не найдено.
     * @return mixed
     */
    function result($row = 0, $field = 0, $default = null) {
        if ($this->seek($row)) {
            $record = $this->fetch();
            if ($record) {
                return $record->$field;
            }
        }
        return $default;
    }

    /**
     * Возвращает запись в виде ассоциативного массива из результата запроса.
     */
    abstract function fetch();

    /**
     * Перемещает указатель на выбранную строку в результате запроса.
     * @param integer $row номер строки.
     */
    abstract function seek($row);

    /**
     * Освобождает ресурсы запроса.
     */
    abstract function free();
}
