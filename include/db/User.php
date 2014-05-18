<?php

class User implements IDbRecord {

    private $id = -1;
    public $username, $password, $firstName, $lastName, $passwordSalt, $createDate;

    /**
     * @return array|bool|int
     */
    public function save()
    {
        if($this->id == -1)
        {
            $this->id = Database::query("INSERT INTO Users
                (username, password, first_name, last_name, password_salt, create_date, auth_token)
                VALUES (?, ?, ?, ?, ?, NOW())",
                $this->username,
                $this->password,
                $this->firstName,
                $this->lastName,
                $this->passwordSalt
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
                last_name = ?, password_salt = ?
                WHERE id = ?",
                $this->username,
                $this->password,
                $this->firstName,
                $this->lastName,
                $this->passwordSalt,
                $this->id
            );
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return !!Database::query("DELETE FROM Users WHERE id = ?", $this->id);
    }

    public static function find($criteria = array())
    {
    }

    /**
     * @param $id int
     * @return null|User
     */
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

        return $user;
    }

    /**
     * @param $username string
     * @return mixed
     */
    public static function findByUsername($username)
    {
        $record = Database::query("SELECT * FROM Users WHERE username = ?", $username);
        if(!$record)
            return null;
        $record = $record[0];

        $user = new User;

        $user->id = $record["id"];
        $user->username = $record["username"];
        $user->password = $record["password"];
        $user->firstName = $record["first_name"];
        $user->lastName = $record["last_name"];
        $user->passwordSalt = $record["password_salt"];
        $user->createDate = $record["create_date"];

        return $user;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
        if(Database::query("SELECT 1 FROM Users WHERE username = ?", $username))
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
} 