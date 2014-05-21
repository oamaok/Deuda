<?php

abstract class DbRecord {

    abstract public function tableName();

    private $tableStructure;

    private $primaryKey = null;

    private $primaryKeyColumn;

    private $fieldNames = array();

    private $fieldValues = array();

    private static $models = array();

    public function __construct()
    {
        if(isset(self::$models[get_class($this)]))
        {
            $model = self::$models[get_class($this)];
            $this->fieldNames = $model->fieldNames;
            $this->fieldValues = $model->fieldValues;
            return $this;
        }

        $columns = $this->getTableStructure();
        foreach($columns as $column)
        {
            $field = $column["Field"];
            $default = $column["Default"];
            $fieldCamelCase = Database::convertCase($field);
            $this->fieldNames[$fieldCamelCase] = $field;
            $this->fieldValues[$fieldCamelCase] = $default;
        }
        $this->getPrimaryKeyColumn();
        self::$models[get_class($this)] = $this;
        return $this;
    }

    public function __get($var)
    {
        if(array_key_exists($var, $this->fieldValues))
            return $this->fieldValues[$var];

        return $this->$var;
    }

    public function __set($var, $val)
    {
        if(array_key_exists($var, $this->fieldValues))
        {
            if($this->getPrimaryKeyColumn() == $var)
                $this->primaryKey = $val;
            $this->fieldValues[$var] = $val;
            return;
        }

        $this->$var = $val;
    }

    public function save()
    {
        if($this->primaryKey)
        {
            $query = "UPDATE " . $this->tableName();
        }
        else
        {
            $columns = "";
            $values = "";
            $arguments = array();
            foreach($this->fieldValues as $field => $value)
            {
                if($field == $this->getPrimaryKeyColumn())
                    continue;
                $columns .= "," . $this->fieldNames[$field];
                $values .= ",?";
                array_push($arguments, $value);
            }
            $columns = substr($columns, 1);
            $values = substr($values, 1);
            $table = $this->tableName();
            $query = "INSERT INTO $table ($columns) VALUES ($values)";
            array_unshift($arguments, $query);

            var_dump($arguments);
            $primaryKey = call_user_func_array("Database::query", $arguments);
            $updated = $this->findByPk($primaryKey);
            $this->copy($updated);
        }
    }

    public function copy($object)
    {
        foreach($object->fieldValues as $field => $value)
        {
            $this->fieldValues[$field] = $value;
        }
    }

    public function delete()
    {

    }

    public function findByPk($value)
    {
        $query = sprintf("SELECT * FROM %s WHERE %s = ?", $this->tableName(), $this->getPrimaryKeyColumn());

        $record = Database::query($query, $value);
        if(!$record)
            return null;
        return $this->fromRecord($record[0]);
    }

    public function find($criteria = array())
    {
        $query = "SELECT * FROM " . $this->tableName();
        $arguments = array();
        if($criteria)
        {
            $query .= " WHERE ";
            foreach($criteria as $field => $value)
            {
                if($arguments)
                    $query .= " AND ";
                array_push($arguments, $value);
                if(!array_key_exists($field, $this->fieldNames))
                {
                    Logger::log("Column '%s' doesn't exist in table %s!", $field, $this->tableName());
                    return false;
                }
                $query .= $this->fieldNames[$field] . " = ?";
            }
        }
        array_unshift($arguments, $query);
        var_dump($arguments);
        $records = call_user_func_array("Database::query", $arguments);
        $objects = array();
        foreach($records as $record)
        {
            array_push($objects, $this->fromRecord($record));
        }
        return $objects;
    }

    public function fromRecord($record)
    {
        $className = get_called_class();
        $object = new $className;

        foreach($record as $key => $value)
        {
            $camelCaseColumn = Database::convertCase($key);
            if(array_key_exists($camelCaseColumn, $object->fieldValues))
            {
                $object->$camelCaseColumn = $record[$key];
            }
        }

        return $object;
    }

    private function getTableStructure()
    {
        if(isset(self::$models[get_class($this)]))
            return self::$models[get_class($this)]->tableStructure;

        if(!isset($this->tableStructure))
        {
            $tables = Database::getTables();
            if(!in_array($this->tableName(), $tables))
            {
                Logger::log("Table '%s' doesn't exist in database %s!", $this->tableName(), Config::DB_DATABASE);
                return false;
            }
            $this->tableStructure = Database::query("SHOW FULL COLUMNS FROM " . $this->tableName());
        }
        return $this->tableStructure;
    }

    public function getPrimaryKeyColumn()
    {
        if(isset(self::$models[get_class($this)]))
            return self::$models[get_class($this)]->primaryKeyColumn;

        if(!isset($this->primaryKeyColumn))
        {
            $columns = $this->getTableStructure();
            foreach($columns as $column)
            {
                if($column["Key"] == "PRI")
                {
                    $this->primaryKeyColumn = Database::convertCase($column["Field"]);
                    break;
                }
            }
        }

        return $this->primaryKeyColumn;
    }

    public static function model($className = __CLASS__)
    {
        if(isset(self::$models[$className]))
            return self::$models[$className];
        else
        {
            $class = self::$models[$className] = new $className;
            return $class;
        }
    }
} 