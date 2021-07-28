<?php

namespace website\barum_gaming_de\php\controller;

use kernel\system;

class IndexController extends System
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRoute() {
        $r = $this->qsa('SELECT url FROM `route` WHERE route_id = 1');

        if ($r) {
            return $r['url'];
        }
    }


}