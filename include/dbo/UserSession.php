<?php

/**
 * Class UserSession
 * @property integer $id
 * @property integer $user
 * @property string $token
 * @property integer $sessionOnly
 * @property string $expireTime
 * @property string $createDate
 */
class UserSession extends DbRecord {

    public function tableName()
    {
        return "UserSessions";
    }
    /**
     * @param $token
     * @return UserSession
     */
    public static function findByToken($token)
    {
        $record = UserSession::model()->find("token = ? AND (expireTime > NOW() OR sessionOnly = 1)", $token);
        if(!$record)
            return null;
        return $record[0];
    }

    public function getUser()
    {
        return User::model()->findByPk($this->user);
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
        $session->user = $user->id;
        $session->token = Auth::generateSessionToken($user->passwordSalt);
        $session->sessionOnly = $sessionOnly;
        $session->expireTime = null;
        $session->createDate = Database::now();
        if($expireTime)
            $session->expireTime = CommonUtil::sqlTimeStamp($expireTime);
        $session->save();

        return $session;
    }

    /**
     * @param string $className
     * @return UserSession
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 