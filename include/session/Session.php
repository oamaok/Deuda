<?php


class Session {

    /**
     * @var User
     * Stores user data if one is logged in.
     *
     */

    private static $sessionUser = null;

    /**
     * @var string
     *
     * Is set or unset when user logs in or logs out.
     * This way we instantly know whether the session cookie
     * is set ot not. Without it a page refresh is needed
     * in order to get the cookie value.
     *
     */

    private static $manualToken = null;

    /**
     * @return mixed
     *
     * Fetches the session user if one is logged in.
     * Otherwise returns null.
     */

    public static function getUser()
    {
        if(!Session::$sessionUser)
        {
            if(!Session::getSessionCookie())
            {
                return null;
            }
            $sessionToken = Session::getSessionCookie();

            $session = UserSession::findByToken($sessionToken);
            if(!$session)
            {
                return null;
            }

            Session::$sessionUser = $session->getUser();
        }

        return Session::$sessionUser;
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
        if(!Session::getUser())
        {
            Session::deleteSessionCookie();
            return;
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
        Session::$manualToken = $token;
        return setcookie(Config::SESSION_COOKIE, $token, $expireTime);
    }

    /**
     * @return null|string
     *
     * Returns the session cookie if one is set.
     *
     */

    private static function getSessionCookie()
    {
        if(Session::$manualToken)
            return Session::$manualToken;
        if(!isset($_COOKIE[Config::SESSION_COOKIE]))
            return null;
        return $_COOKIE[Config::SESSION_COOKIE];
    }

    /**
     * @return bool
     *
     * Deletes the session cookie.
     */

    private static function deleteSessionCookie()
    {
        Session::$manualToken = null;
        return setcookie(Config::SESSION_COOKIE, null, -1);
    }
}