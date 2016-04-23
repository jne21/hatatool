<?php

namespace system;

class ExceptionHandlerOutputErrorLog implements iExceptionHandlerOutput
{
    public static function output($exception, $debug)
    {
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
