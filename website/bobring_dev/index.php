<?php
require_once('route.php');

use website\bobring_dev\php\controller\IndexController;

$project = new IndexController();

$path = explode("/", substr(parse_url($_SERVER['REQUEST_URI'])['path'], 1));

