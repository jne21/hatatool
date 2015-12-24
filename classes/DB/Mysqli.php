<?php
namespace DB;
/**
 * Объект доступа к БД.
 */
final class Mysqli {
	const
		DEFAULT_MODE = 0,
		PRINT_MODE   = 2,
		STOP_MODE    = 4,

		DB_FORCED_VALUE_BEGIN = '{{',
		DB_FORCED_VALUE_END   = '}}';

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

	/**
	 * Создает объект доступа к БД и устанавливает соединение к указанной БД,
	 * @param strin $server сервер БД
	 * @param string $login логин пользователя
	 * @param string $password пароль пользователя
	 * @param string $db_name имя БД
	 */
	function __construct($server, $login, $password, $db_name='') {
//die ("$server, $login, $password, $db_name");
		return $this->connect($server, $login, $password, $db_name);
	}

	/**
	 * Устанавливает соединение с БД.
	 * @param string $server сервер БД
	 * @param string $login логин пользователя
	 * @param string $password пароль пользователя
	 * @param string $db_name имя БД
	 * @return resource ресурс установленного соединения с БД
	 */
	function connect($server, $login, $password, $db_name='') {
		if ($result = mysqli_connect($server, $login, $password)) {
			$this->connection = $result;
		} else {
			$this->lastError = "db::connect($server, $login, $password, $db_name) - Connection error.";
		}
		if ($db_name) {
			$this->database = $this->selectDB($db_name);
		}
		return $result;
	}

	/**
	 * Возвращает ресурс текущего соединения с БД.
	 * @return resource
	 */
	function getLink() {
		return $this->connection;
	}

	/**
	 * Устанавливает текущую БД.
	 * @param string $db_name имя БД.
	 * @return unknown
	 */
	function selectDB($db_name) {
		if (! $result = mysqli_select_db($this->connection, $db_name)) {
			$this->lastError = "db->selectDB($db_name) - Selection error: ".mysqli_error();
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
		if ($queryMode & self::PRINT_MODE) echo("db::query($sqlString)");
		if ($queryMode & self::STOP_MODE) die();
		$result = mysqli_query($this->connection, $sqlString);
		$this->lastError = "db->query($sqlString) - ".mysqli_error($this->connection);
		return $result;
	}

	/**
	 * Возвращает количество строк в SQL выборке
	 * @param resource $resource
	 */
	function numRows($resource) {
		return mysqli_num_rows($resource);
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
	 * Возвращает значение из результата запроса.
	 * @param resource $rs результат запроса
	 * @param number $row номер строки (начиная с 0)
	 * @param number|string $field номер или имя поля в строке
	 * @param unknown $default значение по умолчанию, если заданное поле не найдено.
	 * @return unknown
	 */
	function result($rs, $row = 0, $field = 0, $default = null) {
		if (mysqli_data_seek($rs, $row)) {
			if (($record = mysqli_fetch_row($rs)) && isset($record[$field])) {
				return $record[$field];
			}
		}
		return $default;
	}

	/**
	 * Возвращает запись в виде ассоциативного массива из результата запроса.
	 * @param resource $resource результат запроса
	 */
	function fetch($resource) {
		return mysqli_fetch_assoc($resource);
	}

	/**
	 * Возвращает запись в виде объекта из результатов запроса
	 * @param resource $resource результат запроса
	 */
	function fetchObject($resource) {
		return mysqli_fetch_object($resource);
	}

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
	 * Экранирует спецсимволы в указанных данных.
	 * @param unknown $data данные, в которых требуется экранировать спецсимволы.
	 * @return Ambigous <string, unknown, multitype:>
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
					$value = "'".  mysqli_real_escape_string($this->connection, $data)  ."'";
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
	 * Перемещает указатель на выбранную строку в результате запроса.
	 * @param resource $resource результата запроса
	 * @param integer $row номер строки.
	 */
	function seek($resource, $row) {
		return mysqli_data_seek($resource, $row);
		$this->lastError = mysqli_error($this->connection);
	}

	/**
	 * Освобождает ресурсы запроса.
	 * @param unknown $resource
	 */
	function free($resource) {
		return mysqli_free_result($resource);
	}

	/**
	 * Возвращает автоматически генерируемый ID, используя последний запрос.
	 */
	function insertId() {
		return mysqli_insert_id($this->connection);
//	die('!!'.$this->last_insert_id.'!!');
//		return $this->last_insert_id;
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
		$data = $this->escape($data);
		foreach ($data as $field=>$value) $sqlStack[] = "`$field`=$value";
		return $this->query("UPDATE `$table` SET ".implode(', ', $sqlStack).($whereString==''?'':" WHERE $whereString;"), $queryMode);
	}

	/**
	 * Выполняет SQL-вставку данных в таблицу.
	 * @param string $table имя таблицы
	 * @param array $data ассоциативный массив данных для вставки.
	 * @param string $queryMode режим запроса
	 * @return resource результат выполнения запроса
	 */
	function insert($table, $data, $queryMode=self::DEFAULT_MODE) {
		$data = $this->escape($data);
		$result = $this->query("INSERT INTO `$table` (`".implode('`, `',array_keys($data)).'`) VALUES ('.implode(', ',array_values($data)).")");
//		$this->last_insert_id = mysqli_insert_id($this->connection);
//	die('!'.$this->last_insert_id.'!');
		return $result;
	}

	function replace($table, $data, $queryMode=self::DEFAULT_MODE) {
		$data = $this->escape($data);
		$result = $this->query("REPLACE `$table` (`".implode('`, `',array_keys($data)).'`) VALUES ('.implode(', ',array_values($data)).")");
		return $result;
	}
}
