<?php

class User implements IDbRecord {

    private $id = -1;
    public $username, $password, $first_name, $last_name, $password_salt, $create_date, $auth_token;

    public function save()
    {
        if($this->id == -1)
        {
            return Database::query("INSERT INTO Users
                (username, password, first_name, last_name, password_salt, create_date, auth_token)
                VALUES (?, ?, ?, ?, ?, NOW(), ?)",
                $this->username,
                $this->password,
                $this->first_name,
                $this->last_name,
                $this->password_salt,
                $this->auth_token
            );
        }
        else
        {
            return Database::query("UPDATE Users SET
                username = ?, password = ?, first_name = ?,
                last_name = ?, password_salt = ?, auth_token = ?
                WHERE id = ?",
                $this->username,
                $this->password,
                $this->first_name,
                $this->last_name,
                $this->password_salt,
                $this->auth_token,
                $this->id
            );
        }
    }

    public function delete()
    {

    }

    public function find($criteria = array())
    {

    }

    public static function login($username, $password)
    {

    }
} 