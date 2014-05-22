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

    public static function render($view, $arguments = array())
    {
        ob_start();

        // initialize argument variables
        foreach($arguments as $key => $value)
        {
            $$key = $value;
        }

        require "view" . DIRECTORY_SEPARATOR . $view . ".php";

        // unset argument variables
        foreach($arguments as $key => $value)
        {
            unset($$key);
        }

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
        $fields = array("username" => "");
        $error = "";

        if(isset($_POST["username"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $fields = array("username" => $username);

            $sessionOnly = !isset($_POST["remember"]);
            if(Session::login($username, $password, $sessionOnly))
            {
                // only redirect if login was successful
                CommonUtil::redirect(Config::SITE_BASE);
                return;
            }
            $error = "Invalid username or password.";
        }
        Deuda::render("login", array("fields" => $fields, "error" => $error));
    }

    public static function actionRegister()
    {
        if(Session::getUser())
        {
            CommonUtil::redirect(Config::SITE_BASE);
            return;
        }

        $errorMessages = array();
        $errorFields = array();
        $fields = array();
        $fields = array("username" => "", "firstName" => "", "lastName" => "");
        if(isset($_POST["username"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            $fields = array(
                "username" => $username,
                "firstName" => $firstName,
                "lastName" => $lastName
            );

            if(strlen($username) < 3)
            {
                array_push($errorMessages, "Username is too short.");
                array_push($errorFields, "username");
            }

            if(strlen($firstName) < 1)
            {
                array_push($errorMessages, "You must enter your first name.");
                array_push($errorFields, "firstName");
            }
            if(strlen($lastName) < 1)
            {
                array_push($errorMessages, "You must enter your last name.");
                array_push($errorFields, "lastName");
            }
            if(strlen($password) < 1)
            {
                array_push($errorMessages, "You must have a password.");
                array_push($errorFields, "password");
            }

            if(!$errorMessages && !User::register($username, $password, $firstName, $lastName))
            {
                array_push($errorMessages, "Username is already in use.");
                array_push($errorFields, "username");
            }

            if(!$errorMessages)
            {
                CommonUtil::redirect("login");
                return;
            }
        }

        Deuda::render("register",
            array(
                "fields" => $fields,
                "errorFields" => $errorFields,
                "errorMessages" => $errorMessages
            )
        );
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