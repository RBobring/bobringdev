<?php

namespace website\bobring_dev\php\controller;

use kernel\system;

class IndexController extends System
{
    public $db;


    public function __construct($database = false)
    {
        parent::__construct($database);

        self::setHead();
    }

    public static function setHead()
    {
        parent::$head['title'] = "Bobring Development";

        parent::$js[] = "/website/". parent::$websitePath ."/html/js/script.js?version=0.1";
    }


}