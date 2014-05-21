<?php

/**
 * Class Payment
 * @property integer $id
 * @property integer $from
 * @property integer $to
 * @property double $amount
 * @property string $description
 * @property integer $location
 * @property string $createDate
 */
class Payment extends DbRecord {

    public function tableName()
    {
        return "Payments";
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
            $user = $user->id;
        }

        if(gettype($user) != "integer")
            return null;

        $records = Payment::model()->find("Payments.from = ?", $user);

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
            $user = $user->id;
        }

        if(gettype($user) != "integer")
            return array();

        $records = Payment::model()->find("Payments.to = ?", $user);

        $payments = array();
        foreach($records as $record)
        {
            $payment = Payment::fromRecord($record);
            array_push($payments, $payment);
        }

        return $payments;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 