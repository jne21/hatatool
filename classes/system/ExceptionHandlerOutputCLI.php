<?php

namespace system;

class ExceptionHandlerOutputCLI implements iExceptionHandlerOutput
{
    public static function output($exception, $debug) {
            return 'CLI';
    }
}
