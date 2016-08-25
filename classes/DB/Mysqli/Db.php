<?php

namespace DB\Mysqli;
/**
 * Объект доступа к БД.
 */
final class Db extends \DB\AbstractDb
{

    /**
    * Создает объект доступа к БД и устанавливает соединение к указанной БД,
    * @param array $params пароль пользователя
    */
    function __construct($params) {
        if (is_array($params)) {
            $this->connect($params);
            if ($this->connection) {
                if (array_key_exists(self::DB_NAME, $params)) {
                    $this->selectDb($params[self::DB_NAME]);
                }
            }
        }
    }

    /**
     * Устанавливает соединение с БД.
     * @param array $params
     */
    function connect($params) {
        $result = mysqli_connect($params[self::HOST], $params[self::LOGIN], $params[self::PASSWORD]);
        if ($result) {
            $this->connection = $result;
            if (array_key_exists(self::DB_NAME, $params)) {
                $this->database = $this->selectDB($params[self::DB_NAME]);
            }
        } else {
            $this->lastError = __METHOD__ . ': Connection error '. mysqli_error();
        }
    }

    /**
     * Устанавливает текущую БД.
     * @param string $dbName имя БД.
     * @return unknown
     */
    function selectDB($dbName) {
        $result = mysqli_select_db($this->connection, $dbName);
        if ($result) {
            $this->database = $dbName;
        } else {
            $this->lastError = __METHOD__ . ': Selection error' . mysqli_error($this->connection);
        }
        return $result;
    }

    /**
     * Выполняет запрос к БД.
     * @param string $sqlString SQL-команда.
     * @return resource результат выполнения запроса
     */
    function query($sqlString) {
        $this->lastError = '';
        if ($this->debugMode & self::PRINT_MODE) {
            echo(__METHOD__ . "($sqlString)");
        }
        if ($this->debugMode & self::STOP_MODE) {
            die();
        }
        $result = mysqli_query($this->connection, $sqlString);
        $this->lastError = __METHOD__ . ': ' . mysqli_error($this->connection);
        return $result;
    }

    /**
     * Экранирует спецсимволы в тексте SQL команды
     * @param string $string
     */
    function realEscapeString($string) {
        return mysqli_real_escape_string($this->connection, $string);
    }

    /**
     * Возвращает количество записей, обработанных SQL-командой.
     */
    function affectedRows() {
        return mysqli_affected_rows($this->connection);
    }

    /**
     * Возвращает автоматически генерируемый ID, используя последний запрос.
     */
    function insertId() {
        return mysqli_insert_id($this->connection);
    }

    /**
     * Выполняет SQL-обновление таблицы.
     * @param string $table имя таблицы
     * @param array $data ассоциативный массив данных для обновления.
     * @param string $whereString условие фильтрации
     * @param string $queryMode режим запроса
     * @return resource результат выполнения запроса
     */
    function update($table, $data, $whereString, $queryMode=self::DEFAULT_MODE) {
        $sqlStack = array();
        $escapedData = $this->escape($data);
        foreach ($escapedData as $field=>$value) {
            $sqlStack[] = "`$field`=$value";
        }
        return $this->query("UPDATE `$table` SET " . implode(', ', $sqlStack) . ($whereString==''?'':" WHERE $whereString;"), $queryMode);
    }

    /**
     * Выполняет SQL-вставку данных в таблицу.
     * @param string $table имя таблицы
     * @param array $data ассоциативный массив данных для вставки.
     * @param int $queryMode режим запроса
     * @return resource результат выполнения запроса
     */
    function insert($table, $data) {
        $escapedData = $this->escape($data);
        $result = $this->query("INSERT INTO `$table` (`".implode('`, `',array_keys($escapedData)).'`) VALUES ('.implode(', ',array_values($escapedData)).")");
        return $result;
    }

    function insertMulti($table, $names, $rows)
    {
        $escapedRows = $this->escape($rows);
        foreach ($escapedRows as $row) {
            $data[] = '(' . implode(',', $row) . ')';
        }
        $result = $this->query("INSERT INTO `$table` (`" . implode('`, `', $names) . '`) VALUES ' . implode(', ', $data));
    }

    function replace($table, $data) {
        $escapedData = $this->escape($data);
        $result = $this->query("REPLACE `$table` (`".implode('`, `',array_keys($escapedData)).'`) VALUES ('.implode(', ',array_values($escapedData)).")");
        return $result;
    }

    function delete($tableName, $pKeyValue = null, $pKeyName='id')
    {
        $this->query('DELETE FROM `' . $this->realEscapeString($tableName) . '` WHERE `' . $this->realEscapeString($pKeyName) . '`=' . $this->escape($pKeyValue));
        return $this->affectedRows();
    }

    function getRecordset($sql)
    {
        return new Recordset($this, $this->query($sql));
    }

    function getRecord($sql, $rowNumber=0)
    {
        $recordset = $this->getRecordset($sql);
        $recordset->seek(intval($rowNumber));
        return $recordset->fetch();
    }

    function getValues($sql, $keyName=null)
    {
        $result = [];
        $recordset = $this->getRecordset($sql);
        while($record = $recordset->fetch()) {
            $result[] = $record->getValue($keyName);
        }
        return $result;
    }

}
