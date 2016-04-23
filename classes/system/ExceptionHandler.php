<?php

namespace system;

class ExceptionHandler
{
    public $debug = FALSE;
    public $errorTypes = E_ALL & (~E_STRICT) & (~E_NOTICE);

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

    static function getErrorTypeName($errorCode)
    {
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
    public function handleError($errorCode, $errorMessage, $errorFile=null, $errorLine=null, $errorContext=null)
    {
        if ($errorCode & $this->errorTypes) {
//          echo '#'.__METHOD__.'#<br />';
            $result = [
                'errorCode'    => $errorCode,
                'errorMessage' => $errorMessage,
                'errorFile'    => $errorFile,
                'errorLine'    => $errorLine,
                'errorContext' => $errorContext,
                'trace'        => debug_backtrace($this->debug ? null: DEBUG_BACKTRACE_IGNORE_ARGS)
            ];
            $this->errorHandlerObject->output($result, $this->debug);
            ErrorHandlerOutputErrorLog::output($result, $this->debug);
        }
        return true;
    }

    /**
     * Обработчик исключения
     * @param Exception $exception
     */
    public function handleException($exception=null)
    {
//      echo '#'.__METHOD__.'#<br />';
        $this->exceptionHandlerObject->output($exception, $this->debug);
        ExceptionHandlerOutputErrorLog::output ($exception, $this->debug);
        return true;
    }

    public function setupHandlers()
    {
        $this->errorHandlerObject     = ErrorHandlerOutputFactory::create();
        $this->exceptionHandlerObject = ExceptionHandlerOutputFactory::create();

        $this->oldErrorHandler     = set_error_handler     ([$this, 'handleError']);
        $this->oldExceptionHandler = set_exception_handler ([$this, 'handleException']);
    }

    public static function restoreHandlers()
    {
        set_error_handler($this->oldErrorHandler);
        set_exception_handler($this->oldExceptionHandler);
    }
}
