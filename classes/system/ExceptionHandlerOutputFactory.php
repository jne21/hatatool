<?php

namespace system;

class ExceptionHandlerOutputFactory
{
    public static function create()
    {
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
