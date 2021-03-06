<?php

namespace system;

/**
* Реализует вывод сообщения об ошибке при JSON запросе
* @author jne
*/
class ErrorHandlerOutputJSON implements iErrorHandlerOutput
{
    public static function output($exception, $debug)
        {
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
