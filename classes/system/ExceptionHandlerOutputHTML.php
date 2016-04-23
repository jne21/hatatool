<?php

namespace system;

class ExceptionHandlerOutputHTML implements iExceptionHandlerOutput
{
    public static function output($exception, $debug)
    {
        $context = '';
        foreach ($exception->getContext() as $key=>$value) {
                $context .= $key.': '.print_r($value, TRUE).'<br />';
        }
        $trace = '';
        foreach ($exception->getTrace() as $level=>$data) {
            $args = '';
            $escaped_args = array_map('htmlspecialchars', $data['args']);
//          foreach ($data['args'] as $arg) {
//              $args = htmlspecialchars($arg).'<br />';
//          }
            $args = implode(', ', $escaped_args);
            $trace =
                '<br />#'.$level.' '.$data['file'].':'.$dataine['line'].'<br />'
                .$data['class'].$data['type'].$data['function'].'('.$args.')'.'<br />';
        }

        $message =
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
