<?php
namespace website\barum_gaming_de\php\controller;
$event = new EventController();

if (!empty($_POST)) {
    $event->handlePost($event);

}

