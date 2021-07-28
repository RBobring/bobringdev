<?php
use website\barum_gaming_de\php\controller\IndexController;
$project = new IndexController();

require_once ('route.php');

$view = "landing.php";

if (!isset($_GET['p'])) {
    $view = "landing.php";
} else if ($_GET['p'] == 'pc') {
    $view = "pc.php";

    if (isset($_GET['func']) && $_GET['func'] == 'event') {
        $view = "events.php";
    }
} else if ($_GET['p'] == 'warhammer') {
    $view = "warhammer.php";


} else if ($_GET['p'] == 'magic') {
    $view = "magic.php";
}

$project::$view['main'] = "website/barum_gaming_de/html/views/".$view;




