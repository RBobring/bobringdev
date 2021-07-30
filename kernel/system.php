<?php

namespace kernel;
use JetBrains\PhpStorm\NoReturn;
use kernel\libs\xSql;
use PHPMailer\PHPMailer\PHPMailer;

class System extends xSql
{
    private static $config;
    private static $db;

    private static $path;


    public static $url;
    public static $subDomain = false;

    public static $website = false;
    public static $websitePath = "bobring_dev";
    public static $websiteDatabase;
    public static $websiteStatus;

    public static bool $user = false;

    public static array $head = array();
    public static array $css = array();
    public static array $js = array();

    public static $view = array();
    public static $content = array();
    public static array $error = array();
    public static $logged = false;

    public static $subDatabaseDb;
    public static $subDatabaseConfig;


    public $action;
    public $postData;


    public function __construct($coreDB = false)
    {
        if ($coreDB) {
            self::setSystem();
        } else if (isset($_GET['website']) AND !empty($_GET['website'])) {
            self::setWebsite();
        }

        self::setSubdomain();
        self::setUrl();

        self::prepareHead();
        self::prepareTpl();
        self::prepareNav();

        parent::__construct(self::$config, self::$db);
    }

    public static function auth()
    {
        if (!empty($_SESSION)) {
            if (!empty($_SESSION['user_id'])) {
                self::$logged = true;
            }
        } else {
            self::$logged = false;
        }
        return self::$logged;
    }

    private function setSystem()
    {
        if (file_exists('kernel/connect.php')) include ('kernel/connect.php');
        self::$config = $config;
        self::$db = $db;
    }

    private function setWebsite()
    {
        if (file_exists('website/'.self::$websitePath.'/connect.php')) {
            include ('website/'.self::$websitePath.'/connect.php');
        } else {
            include ('kernel/connect.php');
        }
        self::$config = $config;
        self::$db = $db;
    }

    public function setSubdomain()
    {
        $subdomain = join('.', explode('.', $_SERVER['HTTP_HOST'], -2));
        if ($subdomain && $subdomain !== "www") self::$subDomain = $subdomain;
    }

    public function setUrl() {

        self::$url = "https://".$_SERVER['HTTP_HOST'];
    }

    public function route(): bool
    {
        $website = explode("/", $_GET['website']);

        $r = $this->qsa("SELECT url, path, db, status FROM `website` WHERE url = '".$website[0]."'");

        if ($r) {
            self::$website = $r['url'];
            self::$websitePath = $r['path'];
            self::$websiteDatabase = $r['db'];
            self::$websiteStatus = $r['status'];
        }
        return is_dir('./website/'.self::$websitePath);
    }

    /**
     *  verarbeitet alle post anfragen!
     *  alle Post anfragen werden in formen abgeschickt aber per js verarbeitet und via ajax geschickt!
     *  siehe post.js und ajax.js
     */
    public function handlePost($controller)
    {
        $this->action = array_shift($_POST);
        $this->postData = $_POST;


        if (method_exists($controller, $this->action)
            && is_callable(array($controller, $this->action))) {
            call_user_func(array($controller, $this->action));
        } else {
            die("<br><br><br>handle post not working<br>---");
        }

        die("<br>---<br>POST VERARBEITET<br>---");

    }

    public static function prepareHead()
    {
        self::$head['viewport'] = "width=device-width, user-scalable=no, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0";
        self::$head['author'] = "Rolf Bobring";
        self::$head['format-detection'] = "telephone=no,date=no,address=no,email=no,url=no";
        self::$head['keywords'] = "Web-Developer, Webdeveloping, Frontend, Backend, CSS, HTML, JavaScript, PHP, MySQL";
        self::$head['description'] = "Bobring Web Development";
        self::$head['shortcut icon'] = "/website/bobring_dev/favicon.ico";
        self::$head['title'] = "BOBRING DEVELOPMENT";

        self::$css[] = "/website/bobring_dev/html/css/mobile.css?version=0.1";

        self::$js[] = "/kernel/libs/ajax.js?version=0.1";
        self::$js[] = "/kernel/libs/post.js?version=0.1";
        self::$js[] = "/kernel/libs/script.js?version=0.1";
    }

    public static function prepareTpl()
    {
        self::$view['header'] = "website/".self::$websitePath."/html/tpl/header.php";
        self::$view['content'] = "website/".self::$websitePath."/html/tpl/content.php";
        self::$view['footer'] = "website/".self::$websitePath."/html/tpl/footer.php";
    }

    public static function prepareNav()
    {


        self::$content['nav']['main'] = array(1 => 'Übersicht', 2 => 'Profil', 3 => 'Einstellungen');
    }

    public static function addTpl($file)
    {
        self::$tpl[] = $file;
    }

    public function loadHead()
    {
        echo "<script></script>";
    }
/*
    public static function displayTpl()
    {

        if (empty(self::$tpl['head'])) {
            self::$tpl['head'] = 'html/tpl';
        }

        return self::$tpl;
    }
*/
    /** catch result errors
     * @param $result
     * @param bool $value
     * @return bool
     */
    protected static function result($result, $value=false)
    {
        if (!empty($result))
        {
            if (!empty($value))
            {
                return $value;
            }
            else
            {
                return $result;
            }
        }
        else
        {
            return false;
        }
    }

    public function genRndStr($length = 10, $letter = true, $bigLetter = true, $number = true, $specialChar = false)
    {
        $pool = "";
        $code = "";

        if ($letter)        $pool .= "abcdefghijklmnopqrstuvwxyz";
        if ($bigLetter)     $pool .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if ($number)        $pool .= "1234567890";
        if ($specialChar)   $pool .= "!$%&()=?#+*-.:,;{[]}";

        for ($i = 0; $i <= $length; $i++) $code .= $pool[rand(0, strlen($pool)-1)];

        return $code;
    }

    public function load_controller($controller) {
        require_once ('controller/'.$controller.'.php');
    }

    public function setError($description, $error)
    {
        self::$error[$description] = $error;
    }

    public function checkErrors()
    {
        if (self::$error) {
            foreach (self::$error as $location => $code) {
                if (!empty($code)) {
                    require_once ('views/error.php');
                }
            }
        }
    }

    public function sendMail($to, $sub, $ctn): bool
    {
        require_once('libs/phpmailer/exception.php');
        require_once('libs/phpmailer/phpmailer.php');
        require_once('libs/phpmailer/smtp.php');

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SetFrom("mail@bobring.dev", "Bobring Development");
        $mail->Subject = $sub;
        $mail->Body = $ctn;
        $mail->AddAddress($to);

        if(!$mail->Send()) {
            error_log($mail->ErrorInfo, 0);
            return false;
        } else {
            return true;
        }
    }

}
