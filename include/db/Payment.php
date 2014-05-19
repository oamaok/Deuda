<?php

class Payment implements IDbRecord{
    private $id = -1;
    public $from, $to, $amount, $description, $location, $createDate;

    public function save()
    {
        if($this->id == -1)
        {
            $this->id = Database::query("INSERT INTO Payments
                (from, to, amount, description, location, create_date)
                VALUES (?, ?, ?, ?, ?, NOW())",
                $this->from,
                $this->to,
                $this->amount,
                $this->description,
                $this->location
            );

            $payment = Payment::findById($this->id);
            $this->id = $payment->id;
            $this->createDate = $payment->createDate;

            return $this->id;
        }
        else
        {
            return Database::query("UPDATE Users SET
                from = ?, to = ?, amount = ?,
                description = ?, location = ?
                WHERE id = ?",
                $this->from,
                $this->to,
                $this->amount,
                $this->description,
                $this->location,
                $this->id
            );
        }
    }

    public function delete()
    {
        return Database::query("DELETE FROM Payments WHERE id = ?", $this->id);
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

    public static function findById($id)
    {
        $record = Database::query("SELECT * FROM Payments WHERE id = ?", $id);
        if(!$record)
            return null;
        $record = $record[0];

        return Payment::fromRecord($record);
    }

    /**
     * @param $from int
     * @param $to int
     * @param $amount double
     * @param $description string
     * @return Payment
     */
    public static function createPayment($from, $to, $amount, $description)
    {
        $payment = new Payment;
        $payment->from = $from;
        $payment->to = $to;
        $payment->amount = $amount;
        $payment->description = $description;
        $payment->save();

        return $payment;
    }

    public static function getPaymentsFromUser($user)
    {
        if($user instanceof User)
        {
            $user = $user->getId();
        }

        if(gettype($user) != "integer")
            return null;

        $records = Database::query("SELECT * FROM Payments WHERE from = ?", $user);

        $payments = array();
        foreach($records as $record)
        {
            $payment = Payment::fromRecord($record);
            array_push($payments, $payment);
        }

        return $payments;
    }

    public static function getPaymentsToUser($user)
    {
        if($user instanceof User)
        {
            $user = $user->getId();
        }

        if(gettype($user) != "integer")
            return null;

        $records = Database::query("SELECT * FROM Payments WHERE to = ?", $user);

        $payments = array();
        foreach($records as $record)
        {
            $payment = Payment::fromRecord($record);
            array_push($payments, $payment);
        }

        return $payments;
    }
} 