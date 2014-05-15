<?php

class Logger {
    private static $file;

    public static function log()
    {
        $args = func_get_args();
        $format = array_shift($args);
        $message = vsprintf($format, $args);
        return fputs(Logger::getFile(), date("[d.m.y H:i:s] ") . $message . "\n");
    }

    private static function getFile()
    {
        if(!Logger::$file)
            Logger::$file = fopen(Config::LOG_DIRECTORY . DIRECTORY_SEPARATOR . Config::LOG_FILE, 'a');
        return Logger::$file;
    }
}