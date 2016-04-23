<?php

namespace system;

class ErrorHandlerOutputErrorLog implements iErrorHandlerOutput
{
    public static function output($error, $debug)
    {
        error_log(
            sprintf(
                "Error %s (%s): %s in %s: %s",
                ExceptionHandler::getErrorTypeName($error['errorCode']),
                $error['errorCode'],
                $error['errorMessage'],
                $error['errorFile'],
                $error['errorLine']
            )
            .(php_sapi_name() == 'cli'
                ? ''
                : ' Referer: ' . $_SERVER['REQUEST_URI']
            )
            .($debug
                ? PHP_EOL . print_r($error['errorContext']) . PHP_EOL
                : ''
            )
        );
    }
}
