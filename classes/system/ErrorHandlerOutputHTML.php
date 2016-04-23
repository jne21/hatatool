<?php

namespace system;

class ErrorHandlerOutputHTML implements iErrorHandlerOutput
{
    function __construct()
    {
//		echo __METHOD__.'<br />';
    }

    public static function output($error, $debug)
    {
        $context = '';
        foreach ($error['errorContext'] as $key=>$value) {
            $context .= $key.': '.print_r($value, TRUE).'<br />';
        }
        $trace = '';
        foreach ($error['trace'] as $level=>$data) {
            $args = '';
            $escaped_args = array_map('htmlspecialchars', $data['args']);
//          foreach ($data['args'] as $arg) {
//              $args = htmlspecialchars($arg).'<br />';
//          }
            $args = implode(', ', $escaped_args);
            $trace .=
                '<br />#'.$level.' '.$data['file'].':'.$data['line'].'<br />'
                .$data['class'].$data['type'].$data['function'].'('.$args.')'.'<br />';
        }

        echo sprintf(
            '<pre>Error(%s): %s<br />Source: %s:%s<br />Trace: %s<br />%s',

            ExceptionHandler::getErrorTypeName($error['errorCode']),
            $error['errorMessage'],
            $error['errorFile'],
            $error['errorLine'],
            $trace,
            (($debug && $context) ? 'Context:<br />'.$context : '')
        );
//$this->getPrevious() — Возвращает предыдущее исключение
//$this->getTraceAsString — Получает трассировку стека в виде строки
    }
}
