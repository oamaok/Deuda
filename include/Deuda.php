<?php

class Deuda {
    public static $pageTitle = "";
    public static $outputBuffer = "";
    private static $pages = array(
        "index" => "index",
        "u" => "user",
        "g" => "group",
        "p" => "payment",
        "login" => "login",
        "register" => "register",
        "logout" => "logout",
    );
    public static function run()
    {
        $page = "index";
        $param = null;
        if(isset($_SERVER["PATH_INFO"]))
        {
            $pathInfo = $_SERVER["PATH_INFO"];
            $pathInfo = explode("/", $pathInfo);
            $page = $pathInfo[1];
            if(isset($pathInfo[2]))
                $param = $pathInfo[2];
        }

        if(!array_key_exists($page, Deuda::$pages))
        {
            /*
             * TODO: display 404 error
             */
            return;
        }
        $methodName = "action" . ucfirst(Deuda::$pages[$page]);

        if(method_exists("Deuda", $methodName))
            call_user_func("Deuda::$methodName", $param);
    }

    public static function render($view)
    {
        ob_start();
        require "view" . DIRECTORY_SEPARATOR . $view . ".php";
        Deuda::$outputBuffer = ob_get_clean();
        require "layout/main.php";
    }

    public static function actionIndex()
    {
        if(!Session::getUser())
        {
            CommonUtil::redirect("login");
            return;
        }
        Deuda::render("index");
    }

    public static function actionLogin()
    {
        if(Session::getUser())
        {
            CommonUtil::redirect(Config::SITE_BASE);
            return;
        }

        if(isset($_POST["username"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $sessionOnly = !isset($_POST["remember"]);
            if(Session::login($username, $password, $sessionOnly))
            {
                // only redirect if login was successful
                CommonUtil::redirect(Config::SITE_BASE);
                return;
            }
        }
        Deuda::render("login");
    }

    public static function actionRegister()
    {
        if(Session::getUser())
        {
            CommonUtil::redirect(Config::SITE_BASE);
            return;
        }
        if(isset($_POST["username"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];

            if(User::register($username, $password, $firstName, $lastName))
            {
                CommonUtil::redirect("login");
                return;
            }
        }

        Deuda::render("register");
    }

    public static function actionLogout()
    {
        Session::logout();
        CommonUtil::redirect(Config::SITE_BASE);
    }

    public static function getNavigation()
    {
        if(Session::getUser())
        {
            ob_start();
            require "view" . DIRECTORY_SEPARATOR . "navigation.php";
            return ob_get_clean();
        }
    }

} 