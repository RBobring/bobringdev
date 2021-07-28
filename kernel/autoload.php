<?php
spl_autoload_register(function ($class_name) {

    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';

    if (file_exists($file)) {
        require_once ($file);
    } else {
        return false;
    }

});