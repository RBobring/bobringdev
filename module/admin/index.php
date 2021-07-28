<?php
die("asd");

require_once('route.php');


use kernel\controller\user;
$user = new user();

if ($user::$authStatus == "logged") {
    echo "eingelogged";
} else {
    echo "bitte einloggen";
}

print_r($user::$authStatus);

$user::addTpl("login.tpl.php");
