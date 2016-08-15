<?php

function __autoload($class_name)
{
    $paths = array(
        '/components/',
        '/models/'
    );

    foreach($paths as $path) {
        $path = ROOT . $path . $class_name . '.php';
        if(is_file($path)) {
            require_once $path;
        }
    }

}