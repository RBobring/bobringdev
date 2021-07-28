<?php
use kernel\controller\user;
$user = new user();

//$user::logout();

if ($_GET['user'] === "auth") {

    $test = $_COOKIE['rolf'];

    echo $test. "<br> <br> auth muss fertig gemacht werden";
    die();
}

$view = 'dashboard.php';

if ($user::$logged && $_GET['user'] === 'load') {
    $user::$content['headline'] = "Dashboard Kramsens!";

}

if (!$user::$logged && $_GET['user'] === 'load')
{
    header('Location: https://my.bobring.dev/login');
    die();
}
elseif ($_GET['user'] === 'login')
{

    if ($user::$logged) {
        header('Location: https://my.bobring.dev');
        die();
    }

    if (!empty($_POST)) {
        $account = $user->loadAccount($_POST['user']);

        if (!$account) {
            // $sys['tpl']['error'] = $system->load_content('account_not_exist', false);
        } elseif ($account['status'] == 0) {
            //error -> account banned
        } elseif ($account['status'] == 1) {
            if ($user->verifyUser($_POST['pass'], $account['password'])) {
                $user->login($account['id'], $account['status']);
                echo $user::$url;
                die();
            } else {
                $sys['tpl']['error'] = $system->load_content('wrong_password', false);
            }
        } elseif ($account['status'] == 2) {
            $send_email = $class['account']->new_authcode($account['id'], $account['email']);
            if($send_email) {
                $sys['tpl']['error'] = $system->load_content('email_not_confirmed', false);
            } elseif (!$send_email) {
                $sys['tpl']['error'] = $system->load_content('link_is_send', false);
            }
        }


    } elseif (!empty($_GET['code'])) {
        $verify_code = $user->validateEmail($_GET['code']);

        if ($verify_code) {

            /** TODO: link id benötigt user ID oder userauthID **/

            $test = $user->activateAccount($verify_code);

            echo "Code OK => authID:" . $test;
        }
    }

    $view = 'login.php';
}
elseif ($_GET['user'] === 'register')
{

    if (!empty($_POST)) {

        $user->register($_POST);
        echo "https://my.bobring.dev/welcome";
        die();
    }

    $view = 'register.php';

}
elseif ($_GET['user'] === 'fix')
{
    $view = 'fix.php';
}
elseif ($_GET['user'] === 'welcome')
{
    $view = 'welcome.php';
}

$user::$view['main'] = "module/my/views/".$view;
