<?php


class Session {

    private static $sessionUser = null;

    public static function getSessionUser()
    {
        if(!self::$sessionUser)
        {
            if(!isset($_COOKIE[Config::SESSION_COOKIE]))
                return null;
            $sessionToken = $_COOKIE[Config::SESSION_COOKIE];

            $session = UserSession::findByToken($sessionToken);
            if(!$session)
                return null;

            self::$sessionUser = $session->getUser();
        }

        return self::$sessionUser;
    }

    /**
     * @param $username string
     * @param $password string
     * @param $sessionOnly boolean
     * @return bool
     */
    public static function login($username, $password, $sessionOnly)
    {
        $user = User::login($username, $password);
        if(!$user)
            return false;

        if($sessionOnly)
        {
            $session = UserSession::createForUser($user, true);
            Session::setSessionCookie($session->token, 0);
        }
        else
        {
            $expireTime = time() + 356 * 24 * 60 * 60;
            $session = UserSession::createForUser($user, false, $expireTime);
            Session::setSessionCookie($session->token, $expireTime);
        }
        return true;
    }

    /**
     * @param $token
     * @param $expireTime
     * @return bool
     */
    public static function setSessionCookie($token, $expireTime)
    {
        return setcookie(Config::SESSION_COOKIE, $token, $expireTime);
    }
}