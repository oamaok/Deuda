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
                (username, password, first_name, last_name, password_salt, create_date)
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
            var_dump($user);
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
     * @param $id int
     * @return null|User
     */
    public static function findById($id)
    {
        echo $id;
        $record = Database::query("SELECT * FROM Users WHERE id = ?", $id);
        if(!$record)
            return null;
        $record = $record[0];

        return User::fromRecord($record);
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

        return User::fromRecord($record);
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