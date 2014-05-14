<?php

set_include_path(dirname(__FILE__));

spl_autoload_register(function($className) {
    $includePath = get_include_path();
    if(file_exists($includePath . DIRECTORY_SEPARATOR . $className . ".php"))
    {
        include_once $includePath . DIRECTORY_SEPARATOR . $className . ".php";
        return;
    }
    $includeDirectory = scandir($includePath);

    foreach($includeDirectory as $file)
    {
        if(!is_dir($includePath . DIRECTORY_SEPARATOR . $file))
            continue;
        if(file_exists($includePath . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $className . ".php"))
        {
            include_once $includePath . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $className . ".php";
            return;
        }
    }
    /*
     * TODO:
     *      Throw class not found exception or handle the error in another way
     */
});

?>