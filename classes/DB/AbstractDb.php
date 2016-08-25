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
    protected $connection;

    /**
     * Имя текущей БД.
     * @var string
     */
    protected $database;

    protected $debugMode = self::DEFAULT_MODE;

    function setDebugMode($mode) {
        $this->debugMode = $mode;
    }

    static function makeForcedValue($data) {
    return self::DB_FORCED_VALUE_BEGIN.$data.self::DB_FORCED_VALUE_END;
    }

    static function isForcedValue($data) {
    return (substr($data,0,strlen(self::DB_FORCED_VALUE_BEGIN))==self::DB_FORCED_VALUE_BEGIN && substr($data, -strlen(self::DB_FORCED_VALUE_END))==self::DB_FORCED_VALUE_END);
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
     * @return int Количество обработанных записей
     */
    abstract function update($table, $data, $whereString);

    /**
     * Выполняет SQL-вставку данных в таблицу.
     * @param string $table имя таблицы
     * @param array $data ассоциативный массив данных для вставки.
     * @return int количество обработанных записей
     */
    abstract function insert($table, $data);

    /**
     * Выполняет многострочную SQL-вставку данных в таблицу.
     * @param string $table имя таблицы
     * @param array $names массив имён полей для вставки.
     * @param array $rows массив данных для вставки.
     * @return int количество обработанных записей
     */
    abstract function insertMulti($table, $names, $rows);

    /**
     * Выполняет SQL-replace данных в таблицу.
     * @param string $table имя таблицы
     * @param array $data ассоциативный массив данных для вставки.
     * @return int Количество обработанных записей
     */
    abstract function replace($table, $data);

    /**
     * Выполняет SQL-запрос и возвращает объект DB\AbstractRecordset
     * @param string $sql SQL-запрос
     * @return DB\AbstractRecordset Количество обработанных записей
     */
    abstract function getRecordset($sql);

    /**
     * Выполняет SQL-запрос и возвращает массив из значений заданной колонки.
     * @param string $sql SQL-запрос
     * @param string $keyName название поля. По умолчанию - первое поле в Record.
     * @return array Массив значений
     */
    abstract function getValues($sql, $keyName=null);

    /**
     * Выполняет SQL-запрос и возвращает строку с заданным номером.
     * @param string $sql SQL-запрос
     * @param int $row Номер строки.
     * @return DB\Record
     */
    abstract function getRecord($sql, $rowNumber=0);

    /**
     * Выполняет SQL-запрос и возвращает заданное значение из строки с заданным номером.
     * @param string $sql SQL-запрос
     * @param int $row Номер строки.
     * @param mixed $key Номер или имя поля
     * @return DB\Record
     */
    function getValue($sql, $row=0, $key=0) {
        $recordset = $this->getRecordset($sql);
        $recordset->seek($row);
        $record = $recordset->fetch();
        return $record->getValue($key);
    }
}
