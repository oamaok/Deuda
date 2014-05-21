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

    /**
     * @param string $className
     * @return DebtGroup
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}