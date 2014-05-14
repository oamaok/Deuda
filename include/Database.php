<?php

class Database {

    private static $mysqli;

    public static function query()
    {
        $numArgs = func_num_args();

        if($numArgs != 1 && $numArgs >= 3)
        {
            exit(0);
        }
        $mysqli = Database::getMysqli();
        if($numArgs == 1)
        {

        }
    }

    private static function getMysqli()
    {
        if(Database::$mysqli)
            return Database::$mysqli;

        Database::$mysqli = new mysqli(
            Config::DB_HOSTNAME,
            Config::DB_USERNAME,
            Config::DB_PASSWORD,
            Config::DB_DATABASE
        );
        if(!Database::$mysqli)
        {
            exit(0);
        }
        return Database::$mysqli;
    }
} 