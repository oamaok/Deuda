<?php

class User implements IDbRecord {

    private $id = -1;
    public $username, $password, $firstName, $lastName, $passwordSalt, $createDate, $authToken;

    public function save()
    {
        if($this->id == -1)
        {
            $this->id = Database::query("INSERT INTO Users
                (username, password, first_name, last_name, password_salt, create_date, auth_token)
                VALUES (?, ?, ?, ?, ?, NOW(), ?)",
                $this->username,
                $this->password,
                $this->firstName,
                $this->lastName,
                $this->passwordSalt,
                $this->authToken
            );

            $user = User::findById($this->id);
            $this->id = $user->id;
            $this->createDate = $user->createDate;

            return $this->id;
        }
        else
        {
            return Database::query("UPDATE Users SET
                username = ?, password = ?, first_name = ?,
                last_name = ?, password_salt = ?, auth_token = ?
                WHERE id = ?",
                $this->username,
                $this->password,
                $this->firstName,
                $this->lastName,
                $this->passwordSalt,
                $this->authToken,
                $this->id
            );
        }
    }

    public function delete()
    {

    }

    public static function find($criteria = array())
    {
    }

    public static function findById($id)
    {
        $record = Database::query("SELECT * FROM Users WHERE id = ?", $id);
        if(!$record)
            return null;
        $record = $record[0];

        $user = new User;

        $user->id = $id;
        $user->username = $record["username"];
        $user->password = $record["password"];
        $user->firstName = $record["first_name"];
        $user->lastName = $record["last_name"];
        $user->passwordSalt = $record["password_salt"];
        $user->createDate = $record["create_date"];
        $user->authToken = $record["auth_token"];

        return $user;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function login($username, $password)
    {

    }

    public static function register($username, $password, $firstName, $lastName)
    {
        if(Database::query("SELECT 1 FROM Users WHERE username = ?", $username))
            return false;

        $user = new User;
        $user->username = $username;
        $user->passwordSalt = Auth::generatePasswordSalt();
        $user->password = Auth::hashPassword($password, $user->passwordSalt);
        $user->firstName = $firstName;
        $user->lastName = $lastName;

        if(!$user->save())
            return false;

        return $user;
    }
} 