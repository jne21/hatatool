<?php

namespace common;

class Template
{

    protected $tpl;

    /**
     * Создание экземпляра класса.
     * @param string $text
     */
    function __construct($text)
    {
        $this->setContent($text);
    }

    function setContent($value)
    {
        $this->tpl = $value;
    }

    function getContent()
    {
        return $this->tpl;
    }

    function apply($src=NULL)
    {
        $tpl = preg_replace('/<!---[\s\S]*--->/iU', "", $this->tpl); // remove comments
        if (is_array($src)) {
            foreach($src as $key => $val) {
                preg_match_all('/\{\?'.$key.'\}([\s\S]*)\{\/'.$key.'\}/iU', $tpl, $sources, PREG_SET_ORDER);
                foreach($sources as $source) {
                    $args = preg_split('/\{:'.$key.'\}/iU', $source[1]);
                    $tpl  = str_replace($source[0], $args[empty($val)], $tpl);
                }
                $tpl = str_replace('{'.$key.'}', $val, $tpl);
            }
        }
        return ($tpl);
    }
}
