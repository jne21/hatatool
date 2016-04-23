<?php

namespace system;

class ErrorHandlerOutputCLI implements iErrorHandlerOutput
{
    public static function output($error, $debug)
    {
        echo sprintf(
            "Error %s(%s): \"%s\" in %s: %s %s",

            ExceptionHandler::getErrorTypeName($error['errorCode']),
            $error['errorCode'],
            $error['errorMessage'],
            $error['errorFile'],
            $error['errorLine'],
            (
                $debug
                    ? print_r($error['errorContext'], 1)
                    : ''
            )
        );
    }
}
