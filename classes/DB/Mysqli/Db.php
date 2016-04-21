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
            $this->connection = $this->connect($params);
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
        } else {
            $this->lastError = __METHOD__ . ': Connection error '. mysqli_error();
        }
        if (array_key_exists(self::DB_NAME, $params)) {
            $this->database = $this->selectDB($params[self::DB_NAME]);
        }
    }

    /**
     * Устанавливает текущую БД.
     * @param string $db_name имя БД.
     * @return unknown
     */
    function selectDB($dbName) {
        $result = mysqli_select_db($this->connection, $dbName);
        if (!$result) {
            $this->lastError = __METHOD__ . ': Selection error' . mysqli_error($this->connection);
        }
        return $result;
    }

    /**
     * Выполняет запрос к БД.
     * @param string $sqlString SQL-команда.
     * @param string $queryMode режим работы
     * @return resource результат выполнения запроса
     */
    function query($sqlString, $queryMode=self::DEFAULT_MODE) {
        $this->lastError = '';
        if ($queryMode & self::PRINT_MODE) echo(__METHOD__ . "($sqlString)");
        if ($queryMode & self::STOP_MODE) die();
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
    function insert($table, $data, $queryMode=self::DEFAULT_MODE) {
        $escapedData = $this->escape($data);
        $result = $this->query("INSERT INTO `$table` (`".implode('`, `',array_keys($escapedData)).'`) VALUES ('.implode(', ',array_values($escapedData)).")", $queryMode);
        return $result;
    }

    function replace($table, $data, $queryMode=self::DEFAULT_MODE) {
        $escapedData = $this->escape($data);
        $result = $this->query("REPLACE `$table` (`".implode('`, `',array_keys($escapedData)).'`) VALUES ('.implode(', ',array_values($escapedData)).")", $queryMode);
        return $result;
    }

    function delete($tableName, $pKeyValue = null, $pKeyName='id')
    {
        $this->query('DELETE FROM `' . $this->realEscapeString($tableName) . '` WHERE `' . $this->realEscapeString($pKeyName) . '`=' . $this->escape($pKeyValue));
        return $this->affectedRows();
    }

    function getRecordset($sql, $queryMode)
    {
        return new Recordset($this, $this->query($sql, $queryMode));
    }

    function getRecord($sql, $queryMode)
    {
        return $this->getRecordset($sql, $queryMode)->fetch();
    }

    function getValues($sql, $queryMode)
    {
        $result = [];
        $recordset = $this->getRecordset($sql, $queryMode);
        while($record = $recordset->fetch()) {
            $result[] = $record->getValue();
        }
    }
}
