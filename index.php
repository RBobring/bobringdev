<?php
// Develop Mode ON
if($_SERVER['REMOTE_ADDR'] !== '37.123.122.135') die('Something went wrong :-(');

// Global Settings
if(str_contains($_SERVER['HTTP_USER_AGENT'], 'Trident')) {
    require_once('kernel/views/internetExplorerError.php');
}

// Error Reporting
#error_reporting(E_ALL^E_NOTICE^E_DEPRECATED);
error_reporting(-1); ini_set ('display_errors', 1);

// Start Session
session_start();

// Start System
require_once ('kernel/autoload.php');
use kernel\system;
$system = new System(true);

$system::auth();

if (empty($_GET))
{
    include('website/bobring_dev/index.php');
}
elseif (isset($_GET["website"]))
{
    if (empty($_GET["website"])) {
        die("Bitte eine Website auswählen");
    } elseif ($system->route()) {
        include('website/'.system::$websitePath.'/index.php');
    } else {
        die("Um diese Website zu sehen, musst du zaubern können.");
    }
}
elseif (isset($_GET['user']))
{
    include('module/my/index.php');
}

include("template.php");

/*
echo "<br><br>SESSION:<br><br>";
print_r($_SESSION);
echo "<br><br>SESSIONEND!<br><br>";
*/
/*
$tpl = System::displayTpl();
foreach($tpl as $key => $file) {
    include("website/".$system::$website."/html/tpl/".$file);
}

*/