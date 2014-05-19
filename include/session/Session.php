<?php


class Session {

    private static $sessionUser = null;

    public static function getSessionUser()
    {
        if(!self::$sessionUser)
        {
            if(!Session::getSessionCookie())
                return null;
            $sessionToken = Session::getSessionCookie();

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

    public static function logout()
    {
        if(!Session::getSessionUser())
        {
            Session::deleteSessionCookie();
        }
        $token = Session::getSessionCookie();
        $session = UserSession::findByToken($token);
        $session->delete();
        Session::deleteSessionCookie();
    }
    /**
     * @param $token
     * @param $expireTime
     * @return bool
     */
    private static function setSessionCookie($token, $expireTime)
    {
        return setcookie(Config::SESSION_COOKIE, $token, $expireTime);
    }

    private static function getSessionCookie()
    {
        if(!isset($_COOKIE[Config::SESSION_COOKIE]))
            return null;
        return $_COOKIE[Config::SESSION_COOKIE];
    }

    private static function deleteSessionCookie()
    {
        return setcookie(Config::SESSION_COOKIE, null, -1);
    }
}