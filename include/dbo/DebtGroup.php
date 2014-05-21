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
}