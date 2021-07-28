<?php

namespace website\barum_gaming_de\php\controller;

class EventController extends IndexController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test1()
    {
        $r = $this->qsa("SELECT url FROM `website` WHERE website_id = '1'");
        return $r;
    }

    public function saveEvent()
    {
        echo "daten abspeichern - function";
    }

    public function editData()
    {

    }
}