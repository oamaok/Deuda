<?php

class Database {

    private static $mysqli;

    public static function query()
    {
        $numArgs = func_num_args();

        if(!$numArgs)
        {
            return false;
        }
        /** @var $mysqli mysqli */
        $mysqli = Database::getMysqli();
        $result = false;

        if($numArgs == 1)
        {
            $stmt = $mysqli->prepare(func_get_arg(0));
            $stmt->execute();
            $result = Database::fetch($stmt);
            $stmt->close();

            return $result;
        }

        // if the number of arguments is than two

        $type = "";
        $arguments = array();

        for($i = 1; $i < $numArgs; $i++)
        {
            /*
             *
             * TODO:
             *      better error handling for invalid types
             *
             */
            $argument = func_get_arg($i);
            array_push($arguments, $argument);

            switch(gettype($argument))
            {
                case "string":
                    $type .= 's';
                    break;
                case "double":
                    $type .= 'd';
                    break;
                case "integer":
                    $type .= 'i';
                    break;
                default:
                    return false;
                    break;
            }
        }
        array_unshift($arguments, $type);

        $stmt = $mysqli->prepare(func_get_arg(0));
        call_user_func_array(array($mysqli, "bind_param"), $arguments);
        $stmt->execute();
        $result = Database::fetch($stmt);
        $stmt->close();

        return $result;
    }

    private static function fetch($stmt)
    {
        $stmt->store_result();

        $variables = array();
        $data = array();
        $meta = $stmt->result_metadata();

        while($field = $meta->fetch_field())
            $variables[] = &$data[$field->name];

        call_user_func_array(array($stmt, 'bind_result'), $variables);

        $i = 0;
        $result = array();
        while($stmt->fetch())
        {
            $result[$i] = array();
            foreach($data as $k=>$v)
                $result[$i][$k] = $v;
            $i++;
        }

        return $result;
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
        register_shutdown_function("Database::close");

        return Database::$mysqli;
    }

    public static function close()
    {
        if(Database::$mysqli)
            Database::$mysqli->close();
    }
} 