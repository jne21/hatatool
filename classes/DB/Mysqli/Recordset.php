<?php

namespace DB\Mysqli;

class Recordset extends \DB\AbstractDbRecordset
{

    /**
     * Возвращает количество строк в SQL выборке
     * @param resource $resource
     */
    function numRows() {
        return mysqli_num_rows($this->recordset);
    }

    /**
     * Возвращает запись в виде ассоциативного массива из результата запроса.
     */
    function fetch() {
        $record = mysqli_fetch_assoc($this->recordset);
        $this->db->lastError = mysqli_error($this->db->connection);
        return new DB\Record($record);
    }

    /**
     * Перемещает указатель на выбранную строку в результате запроса.
     * @param integer $row номер строки.
     */
    function seek($row) {
        $result = mysqli_data_seek($this->recordset, $row);
        $this->db->lastError = mysqli_error($this->db->connection);
        return $result;
    }

    /**
     * Освобождает ресурсы запроса.
     */
    function free() {
        $result = mysqli_free_result($this->recordset);
        $this->db->lastError = mysqli_error($this->db->connection);
        return $result;
    }

}
