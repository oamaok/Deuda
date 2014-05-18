<?php

require_once "include/init.php";

$ret = User::register("oamaok", "ebin :D", "Teemu", "Pääkkönen");

var_dump(User::login("",""));