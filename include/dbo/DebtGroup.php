<?php

/**
 * Class DebtGroup
 * @property integer $id
 * @property integer $creator
 * @property string $name
 * @property string $description
 * @property string $createDate
 */
class DebtGroup extends DbRecord {

    public function tableName()
    {
        return "DebtGroups";
    }

    public function getMembers()
    {
        return DebtGroupMember::model()->findByGroup($this->id);
    }

    public function getCreator()
    {
        return User::model()->findByPk($this->creator);
    }

    /**
     * @param string $className
     * @return DebtGroup
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}