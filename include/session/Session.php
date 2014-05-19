<?php


class Session {

    private static $sessionUser = null;

    public static function getSessionUser()
    {
        if(!self::$sessionUser)
        {

        }

        return self::$sessionUser;
    }
}