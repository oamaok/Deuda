<?php

/**
 * Class DebtGroupMember
 *
 * @property int $id
 * @property int $group
 * @property int $user
 * @property int $permissions
 * @property string $createDate
 */
class DebtGroupMember extends DbRecord {
    const PERMISSION_INVITE = 1;
    const PERMISSION_KICK = 2;
    const PERMISSION_OP = 4;

    public function tableName()
    {
        return "DebtGroupMembers";
    }

    public static function findByGroup($group)
    {
        return DebtGroupMember::model()->find("group = ?", $group);
    }

    public function givePermissions($permission)
    {
        $this->permissions |= $permission;
    }

    public function removePermissions($permission)
    {
        $this->permissions &= ~$permission;
    }
    /**
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return !!($this->permissions & $permission);
    }

    /**
     * @param string $className
     * @return DebtGroupMember
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 