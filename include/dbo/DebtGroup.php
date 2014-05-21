<?php

class DebtGroup implements IDbRecord {
    private $id = -1;
    public $name, $description, $creator, $createDate;

    public function save()
    {
        if($this->id == -1)
        {
            $this->id = Database::query("INSERT INTO DebtGroups
                (name, description, creator, create_date)
                VALUES (?, ?, ?, NOW())",
                $this->name,
                $this->description,
                $this->creator
            );

            $debtGroup = DebtGroup::findById($this->id);
            $this->id = $debtGroup->id;
            $this->createDate = $debtGroup->createDate;

            return $this->id;
        }
        else
        {
            return Database::query("UPDATE DebtGroups SET
                name = ?, description = ?,
                creator = ?, create_date = ?
                WHERE id = ?",
                $this->name,
                $this->description,
                $this->creator,
                $this->createDate,
                $this->id
            );
        }
    }

    public function delete()
    {

    }

    public static function findById($id)
    {
        $record = Database::query("SELECT * FROM DebtGroup WHERE id = ?", $id);
        if(!$record)
            return null;
        $record = $record[0];

        return DebtGroup::fromRecord($record);
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