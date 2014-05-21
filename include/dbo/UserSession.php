<?php

class UserSession implements IDbRecord {

    private $id = -1;
    public $user;
    public $token;
    public $sessionOnly;
    public $expireTime;
    public $createDate;

    public function save()
    {
        if($this->id == -1)
        {
            $this->id = Database::query("INSERT INTO UserSessions
                (user, token, session_only, expire_time,create_date)
                VALUES (?, ?, ?, ?, NOW())",
                $this->user,
                $this->token,
                $this->sessionOnly,
                $this->expireTime
            );

            $user = UserSession::findById($this->id);
            $this->id = $user->id;
            $this->createDate = $user->createDate;

            return $this->id;
        }
        else
        {
            return Database::query("UPDATE UserSessions SET
                user = ?, token = ?, session_only = ?,
                expire_time = ? WHERE id = ?",
                $this->user,
                $this->token,
                $this->sessionOnly,
                $this->expireTime,
                $this->id
            );
        }
    }

    public function delete()
    {

    }

    /**
     * @param $record
     * @return UserSession
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

    /**
     * @param $token
     * @return UserSession
     */
    public static function findByToken($token)
    {
        $record = Database::query("SELECT * FROM UserSessions
        WHERE token = ? AND (expire_time > NOW() OR session_only = 1)", $token);
        if(!$record)
            return null;
        $record = $record[0];

        // since mysql isn't case sensitive, perform another check here.
        if($token !=  $record["token"])
            return null;

        return UserSession::fromRecord($record);
    }

    public static function findById($id)
    {
        $record = Database::query("SELECT * FROM UserSessions WHERE id = ?", $id);
        if(!$record)
            return null;
        $record = $record[0];

        return UserSession::fromRecord($record);
    }

    public static function findByUsername()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return User::findById($this->user);
    }

    /**
     * @param $user User
     * @param $sessionOnly boolean
     * @param null $expireTime int
     * @return UserSession
     */
    public static function createForUser($user, $sessionOnly, $expireTime = null)
    {
        $session = new UserSession;
        $session->user = $user->getId();
        $session->token = Auth::generateSessionToken($user->passwordSalt);
        $session->sessionOnly = $sessionOnly;
        $session->expireTime = null;
        if($expireTime)
            $session->expireTime = CommonUtil::sqlTimeStamp($expireTime);
        $session->save();

        return $session;
    }
} 