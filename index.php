<?php

require_once "include/init.php";

$test = Database::query("SELECT * FROM Users");

var_dump($test);