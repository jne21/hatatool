<?
namespace system;
class ExceptionHandler {

	public $debug = FALSE;
	public $errorTypes = (E_ALL & ~E_STRICT) & ~E_NOTICE;

	protected $oldErrorHandler, $oldExceptionHandler;
	protected $errorHandlerObject, $exceptionHandlerObject;

	protected static $errorNames = [
/*     1 */	E_ERROR             => 'E_ERROR',             // Фатальные ошибки времени выполнения. Это неустранимые средствами самого скрипта ошибки, такие как ошибка распределения памяти и т.п. Выполнение скрипта в таком случае прекращается. 	 
/*     2 */	E_WARNING           => 'E_WARNING',           // Предупреждения времени выполнения (не фатальные ошибки). Выполнение скрипта в таком случае не прекращается. 	 
/*     4 */	E_PARSE             => 'E_PARSE',             // Ошибки на этапе компиляции. Должны генерироваться только парсером. 	 
/*     8 */	E_NOTICE            => 'E_NOTICE',            // Уведомления времени выполнения. Указывают на то, что во время выполнения скрипта произошло что-то, что может указывать на ошибку, хотя это может происходить и при обычном выполнении программы. 	 
/*    16 */	E_CORE_ERROR        => 'E_CORE_ERROR',        // Фатальные ошибки, которые происходят во время запуска РНР. Такие ошибки схожи с E_ERROR, за исключением того, что они генерируются ядром PHP. 	 
/*    32 */	E_CORE_WARNING      => 'E_CORE_WARNING',      // Предупреждения (не фатальные ошибки), которые происходят во время начального запуска РНР. Такие предупреждения схожи с E_WARNING, за исключением того, что они генерируются ядром PHP. 	 
/*    64 */	E_COMPILE_ERROR     => 'E_COMPILE_ERROR',     // Фатальные ошибки на этапе компиляции. Такие ошибки схожи с E_ERROR, за исключением того, что они генерируются скриптовым движком Zend. 	 
/*   128 */	E_COMPILE_WARNING   => 'E_COMPILE_WARNING',   // Предупреждения на этапе компиляции (не фатальные ошибки). Такие предупреждения схожи с E_WARNING, за исключением того, что они генерируются скриптовым движком Zend. 	 
/*   256 */	E_USER_ERROR        => 'E_USER_ERROR',        // Сообщения об ошибках сгенерированные пользователем. Такие ошибки схожи с E_ERROR, за исключением того, что они генерируются в коде скрипта средствами функции PHP trigger_error(). 	 
/*   512 */	E_USER_WARNING      => 'E_USER_WARNING',      // Предупреждения сгенерированные пользователем. Такие предупреждения схожи с E_WARNING, за исключением того, что они генерируются в коде скрипта средствами функции PHP trigger_error(). 	 
/*  1024 */	E_USER_NOTICE       => 'E_USER_NOTICE',       // Уведомления сгенерированные пользователем. Такие уведомления схожи с E_NOTICE, за исключением того, что они генерируются в коде скрипта, средствами функции PHP trigger_error(). 	 
/*  2048 */	E_STRICT            => 'E_STRICT',            // Включаются для того, чтобы PHP предлагал изменения в коде, которые обеспечат лучшее взаимодействие и совместимость кода. 	Начиная с PHP 5, но не включены в E_ALL вплоть до PHP 5.4.0
/*  4096 */	E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR', // Фатальные ошибки с возможностью обработки. Такие ошибки указывают, что, вероятно, возникла опасная ситуация, но при этом, скриптовый движок остается в стабильном состоянии. Если такая ошибка не обрабатывается функцией, определенной пользователем для обработки ошибок (см. set_error_handler()), выполнение приложения прерывается, как происходит при ошибках E_ERROR. 	Начиная с PHP 5.2.0
/*  8192 */	E_DEPRECATED        => 'E_DEPRECATED',        // Уведомления времени выполнения об использовании устаревших конструкций. Включаются для того, чтобы получать предупреждения о коде, который не будет работать в следующих версиях PHP. 	Начиная с PHP 5.3.0
/* 16384 */	E_USER_DEPRECATED   => 'E_USER_DEPRECATED',   // Уведомления времени выполнения об использовании устаревших конструкций, сгенерированные пользователем. Такие уведомления схожи с E_DEPRECATED за исключением того, что они генерируются в коде скрипта, с помощью функции PHP trigger_error(). 	Начиная с PHP 5.3.0
/* 32767 */	E_ALL               => 'E_ALL'                // Все поддерживаемые ошибки и предупреждения, за исключением ошибок E_STRICT до PHP 5.4.0. 	32767 в PHP 5.4.x, 30719 в PHP 5.3.x, 6143 в PHP 5.2.x, 2047 ранее 
	];
	
	static function getErrorTypeName($errorCode) {
		return self::$errorNames[$errorCode];
	}
	
	/**
	 * Обработчик стандартной ошибки
	 * @param int $errorCode код ошибки
	 * @param string $errorMessage текстовое сообщение об ошибке
	 * @param string $errorfile файл, вызвавший ошибку
	 * @param int $errorline номер строки
	 * @param array $errorContext полное системное окружение  
	 **/
	public function handleError($errorCode, $errorMessage, $errorFile=NULL, $errorLine=NULL, $errorContext=NULL) {
		if ($errorCode & $this->errorTypes) {
//			echo '#'.__METHOD__.'#<br />';
			$result = [
				'errorCode'    => $errorCode,
				'errorMessage' => $errorMessage,
				'errorFile'    => $errorFile,
				'errorLine'    => $errorLine,
				'errorContext' => $errorContext,
				'trace'        => debug_backtrace($this->debug ? NULL: DEBUG_BACKTRACE_IGNORE_ARGS)
			];
			$this->errorHandlerObject->output($result, $this->debug);
	
			ErrorHandlerOutputErrorLog::output ($result, $this->debug);
		}
		return TRUE;
	}

	/**
	 * Обработчик исключения
	 * @param Exception $exception
	 */
	public function handleException($exception=NULL) {
//		echo '#'.__METHOD__.'#<br />';
		$this->exceptionHandlerObject->output($exception, $this->debug);
		ExceptionHandlerOutputErrorLog::output ($exception, $this->debug);
		return TRUE;
	}
	
	public function setupHandlers() {
		$this->errorHandlerObject     = ErrorHandlerOutputFactory::getHandlerOutput();
		$this->exceptionHandlerObject = ExceptionHandlerOutputFactory::getHandlerOutput();
		
		$this->oldErrorHandler     = set_error_handler     ([$this, 'handleError']);
		$this->oldExceptionHandler = set_exception_handler ([$this, 'handleException']);
	}

	public static function restoreHandlers() {
		set_error_handler($this->oldErrorHandler);
		set_exception_handler($this->oldExceptionHandler);
	}

}

class ExceptionHandlerOutputFactory {

	public static function getHandlerOutput () {
		if (php_sapi_name() == 'cli') {
			return new ExceptionHandlerOutputCLI();
		}
		else {
			if ($_SERVER['HTTP_ACCEPT']=='text/json') {
				return new ExceptionHandlerOutputJSON();
			}
			else {
				return new ExceptionHandlerOutputHTML();
			}
		}
	}
}

class ErrorHandlerOutputFactory {

	public static function getHandlerOutput () {
		if (php_sapi_name() == 'cli') {
			return new ErrorHandlerOutputCLI();
		}
		else {
			if ($_SERVER['HTTP_ACCEPT']=='text/json') {
				return new ErrorHandlerOutputJSON();
			}
			else {
				return new ErrorHandlerOutputHTML();
			}
		}
	}
}

// ---------------------------------- Exception output -----------------------------------------------
interface iExceptionHandlerOutput {
//	public static function setTemplate($mixed);
	public static function output($error, $debug);
}

class ExceptionHandlerOutputCLI implements iExceptionHandlerOutput {

//	public static function setTemplate($template) {}
	public static function output($exception, $debug) {
		return 'CLI';
	}
}

class ExceptionHandlerOutputHTML implements iExceptionHandlerOutput {

//	function __construct() {
//		echo __METHOD__.'<br />';
//	}

	public static function output($exception, $debug) {
		$context = '';
		foreach ($exception->getContext() as $key=>$value) {
			$context .= $key.': '.print_r($value, TRUE).'<br />';
		}
		$trace = '';
		foreach ($exception->getTrace() as $level=>$data) {
			$args = '';
			$escaped_args = array_map('htmlspecialchars', $data['args']);
//			foreach ($data['args'] as $arg) {
//				$args = htmlspecialchars($arg).'<br />';
//			}
			$args = implode(', ', $escaped_args);
			$trace =
				'<br />#'.$level.' '.$data['file'].':'.$dataine['line'].'<br />'
				.$data['class'].$data['type'].$data['function'].'('.$args.')'.'<br />';
		}

		$message =
//			print_r($exception, true).
			'<pre>'
			.'Exception('.$exception->getCode().'): '.$exception->getMessage().'<br />'
			.'Source: '.$exception->getFile().':'.$exception->getLine().'<br />'
			.'Trace: '.$trace/*print_r($exception->getTrace(), true)*/.'<br />'
			.(($debug && $context) ? 'Context:<br />'.$context : '');
//$this->getPrevious() — Возвращает предыдущее исключение
//$this->getTraceAsString — Получает трассировку стека в виде строки
		echo $message;
	}

//	public static function setTemplate($template) {}
}

class ExceptionHandlerOutputJSON implements iExceptionHandlerOutput {
	public static function output($exception, $debug) {
		header('HTTP/1.0 500 Internal Server Error', true, 500);
		header('Status: 500 Internal Server Error', true, 500);
		$response = array(
			'error' => true,
			'message' => '',
		);
		if($debug){
			$response['message'] = $exception->getMessage();
		} else {
			$response['message'] = self::$productionMessage;
		}
		exit(json_encode($response));
	}
}

class ExceptionHandlerOutputErrorLog implements iExceptionHandlerOutput {
	public static function output($exception, $debug) {
		$context = '';
		foreach ($exception->getContext() as $key=>$value) {
			$context .= ' '.$key.': '.print_r($value, TRUE);
		}

		error_log(
			"Exception(".$exception->getCode()."): ".$exception->getMessage().' in '.$exception->getFile().':'.$exception->getLine()
			.(
				php_sapi_name() == 'cli'
				? ''
				: " Referer: {$_SERVER['REQUEST_URI']}"
			)
			.(
				$context
				? ' Context:'.$context
				: ''
			)
		);

	}
}

// ---------------------------------- Error output -----------------------------------------------
interface iErrorHandlerOutput {
	public static function output($error, $debug);
}

class ErrorHandlerOutputCLI implements iErrorHandlerOutput {

	public static function output($error, $debug) {
		echo
			"Error ".ExceptionHandler::getErrorTypeName($error['errorCode'])."({$error['errorCode']}): \"{$error['errorMessage']}\" in {$error['errorFile']}: {$error['errorLine']}"
			.(
				$debug
				? print_r($error['errorContext'])
				: ''
			);
	}
}

class ErrorHandlerOutputHTML implements iErrorHandlerOutput {

	function __construct() {
//		echo __METHOD__.'<br />';
	}
/*
	public static function output($error, $debug) {
		$result = print_r($error, 1);
		echo "HTML-OUTPUT[$debug]($result)<br />";
	}
*/
	public static function output($error, $debug) {
		$context = '';
		foreach ($error['errorContext'] as $key=>$value) {
			$context .= $key.': '.print_r($value, TRUE).'<br />';
		}
		$trace = '';
//echo '<pre>'.print_r($error['trace'], true);
		foreach ($error['trace'] as $level=>$data) {
			$args = '';
			$escaped_args = array_map('htmlspecialchars', $data['args']);
//			foreach ($data['args'] as $arg) {
//				$args = htmlspecialchars($arg).'<br />';
//			}
			$args = implode(', ', $escaped_args);
			$trace .=
				'<br />#'.$level.' '.$data['file'].':'.$data['line'].'<br />'
				.$data['class'].$data['type'].$data['function'].'('.$args.')'.'<br />';
		}

		$message =
//			print_r($exception, true).
			'<pre>'
			.'Error('.ExceptionHandler::getErrorTypeName($error['errorCode']).'): '.$error['errorMessage'].'<br />'
			.'Source: '.$error['errorFile'].':'.$error['errorLine'].'<br />'
			.'Trace: '.$trace/*print_r($exception->getTrace(), true)*/.'<br />'
			.(($debug && $context) ? 'Context:<br />'.$context : '');
//$this->getPrevious() — Возвращает предыдущее исключение
//$this->getTraceAsString — Получает трассировку стека в виде строки
		echo $message;
	}

}

/**
 * Реализует вывод сообщения об ошибке при JSON запросе
 * @author Eugene
 *
 */
class ErrorHandlerOutputJSON implements iErrorHandlerOutput {

	public static function output($exception, $debug) {
		header('HTTP/1.0 500 Internal Server Error', true, 500);
		header('Status: 500 Internal Server Error', true, 500);
		$response = array(
			'error' => true,
			'message' => '',
		);
		if($debug){
			$response['message'] = $exception->getMessage();
		} else {
			$response['message'] = self::$productionMessage;
		}
		exit(json_encode($response));
	}
}

class ErrorHandlerOutputErrorLog implements iErrorHandlerOutput {
	public static function output($error, $debug) {
		error_log(
			"Error ".ExceptionHandler::getErrorTypeName($error['errorCode'])." ({$error['errorCode']}): \"{$error['errorMessage']}\" in {$error['errorFile']}: {$error['errorLine']}"
			.(
				php_sapi_name() == 'cli'
				? ''
				: " Referer: {$_SERVER['REQUEST_URI']}"
			)
			.(
				$debug
				? PHP_EOL.print_r($error['errorContext']).PHP_EOL
				: ''
			)
		);
	}
}

class BaseException extends \Exception {

	function __construct($message, $code=0, $previous=NULL) {
		parent::__construct($message, $code=0, $previous=NULL);
	}
}
?>