<?php

class Database {

    private static $mysqli;

    /**
     * @return array|bool|int
     *
     */
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
            if(!$stmt)
            {
                Logger::log('$mysqli->prepare failed (%s).', $mysqli->error);
                return false;
            }
            $stmt->execute();
            if($stmt->errno)
            {
                Logger::log('$mysqli->execute failed (%s).', $stmt->error);
                return false;
            }
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
        if(!$stmt)
        {
            Logger::log('$mysqli->prepare failed (%s).', $mysqli->error);
            return false;
        }

        if(!call_user_func_array(array($stmt, "bind_param"), CommonUtil::arrayToReferences($arguments)))
        {
            Logger::log('$mysqli->bind_param failed.');
            return false;
        }
        $stmt->execute();
        if($stmt->errno)
        {
            Logger::log('$mysqli->execute failed (%s).', $stmt->error);
            return false;
        }
        if($stmt->affected_rows == -1)
            $result = Database::fetch($stmt);
        else
            $result = $stmt->insert_id;

        $stmt->close();

        return $result;
    }

    private static function fetch($stmt)
    {
        $stmt->store_result();

        $variables = array();
        $data = array();
        $meta = $stmt->result_metadata();

        if(!$meta)
            return true;

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

    public static function lastError()
    {
        return Database::$mysqli->error;
    }
} 