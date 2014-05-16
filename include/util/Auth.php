<?php

class Auth {

    public static function hashPassword($password, $passwordSalt)
    {
        return hash(Config::AUTH_ALGORITHM, $passwordSalt . $password);
    }

    public static function generatePasswordSalt()
    {
        $hash = hash("sha512", microtime(true));
        $binary = pack("H*", $hash);
        return base64_encode($binary);
    }

    public static function verifyPassword($password, $passwordHash, $passwordSalt)
    {
        return Auth::hashPassword($password, $passwordSalt) == $passwordHash;
    }
} 