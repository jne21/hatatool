<?php

namespace system;

class ErrorHandlerOutputFactory
{
    public static function create()
    {
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
