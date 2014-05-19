<?php

class DebtGroupMember implements IDbRecord {
    private $id = -1;

    public function save()
    {

    }

    public function delete()
    {

    }

    /**
     * @param $record
     * @return mixed
     */
    public static function fromRecord($record)
    {
        $className = __CLASS__;
        $object = new $className;

        foreach($record as $key => $value)
        {
            $camelCaseColumn = Database::convertCase($key);
            if(property_exists($object, $camelCaseColumn))
                $object->$camelCaseColumn = $record[$key];
        }

        return $object;
    }

} 