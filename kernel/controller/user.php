<?php

namespace kernel\controller;
use kernel\system;

class user extends System
{
    public function __construct()
    {
        parent::__construct();
    }

    public function register($data): bool
    {

        $ins['username'] = $data['username'];
        $ins['firstname'] = $data['firstname'];
        $ins['lastname'] = $data['lastname'];
        $user_id = $this->mi($ins, '`user`');

        unset($ins);

        $ins['email'] = $data['email'];
        $ins['status'] = 2;
        $ins['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $user_auth_id = $this->mi($ins, '`user_auth`');


        if ($user_id && $user_auth_id) {
            return $this->sendRegisterMail($user_id, $user_auth_id, $data['firstname'], $data['email']);
        } else {
            return false;
        }

    }

    private function sendRegisterMail($user_id, $user_auth_id, $firstname, $email): bool
    {

        $ctn = "Herzlich Willkommen<br>";
        $sbj = "Moin ". $firstname;

        $link = $this->createLink($user_auth_id);

        $ctn .= "<br> Hier der Link:<br><br>";
        $ctn .= '<a href="https://my.bobring.dev/login/'. $link . '">Email bestätigen!</a>';

        $this->sendMail($email, $sbj, $ctn);

        return true;
    }

    public function loadAccount($email)
    {
        preg_match_all("/ *@(gmail.com|googlemail.com)/",$email, $gmail);

        if (!empty($gmail[0][0]))
        {
            preg_match_all("/.*@/",$email, $newmail);
            $mail[0] = $newmail[0][0]."gmail.com";
            $mail[1] = $newmail[0][0]."googlemail.com";
            $result = $this->qsa('
				SELECT 
					user_auth_id as id,
					email,
					password,
					status
				FROM 
					`user_auth`
				WHERE 
					(email = "'.$mail[0].'"
				OR	email = "'.$mail[1].'")
				ORDER BY 
					user_auth_id DESC LIMIT 1
			');
        }
        else
        {
            $result = $this->qsa('
				SELECT 
					user_auth_id as id,
					email,
					password,
					status
				FROM 
					`user_auth`
				WHERE 
					email = "'.$email.'"
				ORDER BY 
					user_auth_id DESC LIMIT 1
			');
        }
        return user::result($result);
    }

    public function verifyUser($pw, $hash)
    {
        if (password_verify($pw, $hash)) {
            return true;
        } else {
            return false;
        }
    }

    public function login($id, $pw)
    {

        $_SESSION['user_id'] = $id;

        /*

        $result = $this->qsa('
			SELECT
				t_user.user_id,
				t_user.firstname,
				t_user.lastname,
				t_user_login.email,
				t_user_login.pass,
				t_user_pic.media_id as pic
			FROM
				`user` as t_user,
				`user_login` as t_user_login,
				`user_pic` as t_user_pic
			WHERE
				t_user.user_id = "'.$func_userid.'"
			AND t_user_login.user_id = "'.$func_userid.'"
			AND t_user_login.status = 1
			AND t_user_pic.user_id = "'.$func_userid.'"
			AND t_user_pic.status = 1
		');

        if ($result)
        {
            if (!$_COOKIE['trans'])
            {
                setcookie("trans", session_id(), time()+2592000, "/");

                $ins['user_id'] = $result['user_id'];
                $ins['sess_id'] = session_id();
                $ins['device'] = $this->device;
                $ins['user_agent'] = $this->user_agent;
                $this->mi($ins,'`user_device`');
            }
            else
            {
                if ($_COOKIE['trans'] != session_id())
                {
                    $this->qa('DELETE FROM `user_device` WHERE sess_id="'.$_COOKIE['trans'].'"');

                    $ins['user_id'] = $result['user_id'];
                    $ins['sess_id'] = session_id();
                    $ins['device'] = $this->device;
                    $ins['user_agent'] = $this->user_agent;
                    $this->mi($ins,'`user_device`');

                    setcookie("trans", session_id(), time()+2592000, "/");
                } else {
                    $ins['user_id'] = $result['user_id'];
                    $ins['sess_id'] = session_id();
                    $ins['device'] = $this->device;
                    $ins['user_agent'] = $this->user_agent;
                    $this->mi($ins,'`user_device`');
                }
            }
            if ($cookie AND !$token)
            {
                setcookie("checksum", $result['user_id'], time()+2592000, "/");
                setcookie("token", $result['pass'], time()+2592000, "/");
                unset($result['pass']);
                $_SESSION['user'] = $result;
            }
            elseif ($cookie AND $token)
            {
                if ($result['pass'] == $token) {
                    setcookie("checksum", $result['user_id'], time()+2592000, "/");
                    setcookie("token", $result['pass'], time()+2592000, "/");
                    unset($result['pass']);
                    $_SESSION['user'] = $result;
                }
            }
            else
            {
                setcookie("checksum", 0, time()+2592000, "/");
                unset($result['pass']);
                $_SESSION['user'] = $result;
            }
        }
        else {
            session_destroy();
            setcookie("checksum", "", time()-3600, "/");
        }
        */
    }

    public static function logout()
    {

        session_destroy();
        session_unset();
        /* TODO: function for logout */
    }

    private function createLink(): string
    {

        while (true) {
            $code = $this->genRndStr(6);

            $exist = $this->qsa('
				SELECT
					link
				FROM
					`link`
				WHERE
					BINARY code = "'.$code.'"
			');
            if (empty($exist)) {
                break;
            }
        }

        $ins['link'] = $code;
        $ins['status'] = 1;
        $this->mi($ins, '`link`');

        return $code;

    }

    public function validateEmail($code): array|bool|null
    {
        $query = $this->qsa('
				SELECT
					link_id
				FROM
					`link`
				WHERE
					link = "'.$code.'"
			');

        return $query;
    }


    public function activateAccount($userID)
    {
        $rolf = $this->mu(array('status' => '1'),  array('user_auth_id' => $userID), '`user_auth`');

        return $rolf;
    }



}