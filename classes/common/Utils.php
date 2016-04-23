<?php

namespace common;

class Utils
{
    function removedir($name) {
        if (is_dir($name)) {
            if ($dir = opendir($name)) {
                while (($file = readdir($dir)) !== false) {
                    $newpath = "$name/$file";
                    if (is_dir($newpath)) {
                        if (substr($file,-1,1)!=".") {
                            removedir($newpath);
                        }
                    } else {
                        unlink($newpath);
                    }
                }
                closedir($dir);
            }
            if (substr($file,-1,1) != ".") {
                rmdir($name);
            }
        }
    }

    function d($var, $stop = false)
    {
        echo '<pre>';
        var_dum($var, true);
        echo '</pre>';
        if ($stop) {
            die();
        }
    }
}