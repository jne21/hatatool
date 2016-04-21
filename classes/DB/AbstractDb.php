<?php

namespace DB;

abstract class AbstractDb
{
    const DEFAULT_MODE = 0;
    const PRINT_MODE   = 2;
    const STOP_MODE    = 4;

    const DB_FORCED_VALUE_BEGIN = '{{';
    const DB_FORCED_VALUE_END   = '}}';

    const LOGIN    = 'login';
    const PASSWORD = 'password';
    const HOST     = 'host';
    const DB_NAME  = 'db_name';

    /**
     * Последний выполненный SQL-оператор, вызвавший ошибку.
     * @var string
     */
    public $lastError;

    /**
     * Соединение с БД.
     * @var resource
     */

    private $connection;
    /**
     * Имя текущей БД.
     * @var string
     */

    private $database;


    static function makeForcedValue($data) {
	return self::DB_FORCED_VALUE_BEGIN.$data.self::DB_FORCED_VALUE_END;
    }

    static function isForcedValue($data) {
	return	(substr($data,0,strlen(self::DB_FORCED_VALUE_BEGIN))==self::DB_FORCED_VALUE_BEGIN && substr($data, -strlen(self::DB_FORCED_VALUE_END))==self::DB_FORCED_VALUE_END);
    }

    static function getForcedValue($data) {
        return substr($data, strlen(self::DB_FORCED_VALUE_BEGIN), - strlen(self::DB_FORCED_VALUE_END));
    }

    /**
     * Экранирует спецсимволы в указанных данных для непосредственной вставки в SQL-запрос.
     * @param unknown $data данные, в которых требуется экранировать спецсимволы.
     * @return string
     */
    function escape($data) {
        if (is_array($data)) {
            $value = array_map(array($this, 'escape'), $data);
        } else {
            if (!isset($data) || is_null($data)) {
                $value="NULL";
            } elseif (is_string($data)) {
                if (self::isForcedValue($data)) {
                    $value = self::getForcedValue($data);
                } else {
                    $value = "'".  $this->realEscapeString($data)  ."'";
                }
            } elseif (is_bool($data)===TRUE) {
                $value = $data ? 'TRUE' : 'FALSE';
            } else {
                $value = $data;
            }
        }
        return $value;
    }

    /**
     * Возвращает ресурс текущего соединения с БД.
     * @return resource
     */
    function getConnection() {
        return $this->connection;
    }

    abstract function connect($parameters);

    /**
     * Устанавливает текущую БД.
     * @param string $dbName имя БД.
     * @return unknown
     */
    abstract function selectDb($dbName);
    abstract function query($sql);
    abstract function delete($tableName, $pKeyValue=null, $pKeyName='id');

    /**
     * Экранирует спецсимволы в тексте SQL команды
     * @param string $string
     */
    abstract function realEscapeString($string);

    /**
     * Возвращает количество записей, обработанных SQL-командой.
     */
    abstract function affectedRows();

    /**
     * Возвращает автоматически генерируемый ID, используя последний запрос.
     */
    abstract function insertId();

    /**
     * Выполняет SQL-обновление таблицы.
     * @param string $table имя таблицы
     * @param array $data ассоциативный массив данных для обновления.
     * @param string $whereString условие фильтрации
     * @param string $queryMode режим запроса
     * @return int Количество обработанных записей
     */
    abstract function update($table, $data, $whereString, $queryMode=self::DEFAULT_MODE);

    /**
     * Выполняет SQL-вставку данных в таблицу.
     * @param string $table имя таблицы
     * @param array $data ассоциативный массив данных для вставки.
     * @param string $queryMode режим запроса
     * @return int количество обработанных записей
     */
    abstract function insert($table, $data, $queryMode=self::DEFAULT_MODE);

    /**
     * Выполняет SQL-replace данных в таблицу.
     * @param string $table имя таблицы
     * @param array $data ассоциативный массив данных для вставки.
     * @param string $queryMode режим запроса
     * @return int Количество обработанных записей
     */
    abstract function replace($table, $data, $queryMode=self::DEFAULT_MODE);
    abstract function getRecordset($sql, $queryMode=self::DEFAULT_MODE);
    abstract function getValues($sql, $queryMode=self::DEFAULT_MODE);
    abstract function getRecord($sql, $queryMode=self::DEFAULT_MODE);
}
