<?php

class DebtGroupMember implements IDbRecord {

    const PERMISSION_INVITE = 1;
    const PERMISSION_KICK = 2;
    const PERMISSION_OP = 4;

    private $id = -1;
    public $group, $user, $permissions = 0, $createDate;
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

    public function givePermissions($permission)
    {
        $this->permissions |= $permission;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return !!($this->permissions & $permission);
    }
} 