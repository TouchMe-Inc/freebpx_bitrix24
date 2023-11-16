<?php

spl_autoload_extensions(".php");
spl_autoload_register(function ($className) {
    $classFile = __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $className) . ".php";

    if (!file_exists($classFile)) {
        return;
    }

    require_once $classFile;
});
