<?php

namespace common;

class TemplateFile extends Template
{
    /**
     * Создание экземпляра класса из файла
     * @param string $fileSpec полная спецификация имени файла.
     */
    function __construct($fileSpec)
    {
        try {
            $tpl = file_get_contents($fileSpec);
        } catch (\Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
        }
        parent::__construct($tpl);
    }
}
