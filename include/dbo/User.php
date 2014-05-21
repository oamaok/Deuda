<?php

/**
 * Class User
 * @property integer $id
 * @property string  $username
 * @property string $password
 * @property string $firstName
 * @property string $lastName
 * @property string $passwordSalt
 * @property string $createDate
 *
 */
class User extends DbRecord {

    /**
     * @return string
     */
    public function tableName()
    {
        return "Users";
    }

    /**
     * @param $username string
     * @return mixed
     */
    public static function findByUsername($username)
    {
        $users = User::model()->find("username = ?", $username);
        return $users[0];
    }

    /**
     * @param $username string
     * @param $password string
     * @return mixed
     *
     * Returns User object if successful, otherwise null.
     */
    public static function login($username, $password)
    {
        $user = User::findByUsername($username);
        if(!$user)
            return null;

        $passwordHash = $user->password;
        $passwordSalt = $user->passwordSalt;
        if(Auth::verifyPassword($password, $passwordHash, $passwordSalt))
            return $user;
        return null;
    }

    /**
     * @param $username string
     * @param $password string
     * @param $firstName string
     * @param $lastName string
     * @return mixed
     */
    public static function register($username, $password, $firstName, $lastName)
    {
        if(User::findByUsername($username))
            return null;

        $user = new User;
        $user->username = $username;
        $user->passwordSalt = Auth::generatePasswordSalt();
        $user->password = Auth::hashPassword($password, $user->passwordSalt);
        $user->firstName = $firstName;
        $user->lastName = $lastName;

        if(!$user->save())
            return null;

        return $user;
    }

    /**
     * @param string $className
     * @return User
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 