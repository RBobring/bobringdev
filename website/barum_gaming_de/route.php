<?php
use kernel\controller\router;

Router::add('/website/barum-gaming.de/events', function() {
    include('website/barum_gaming_de/php/views/events.php');
}, "both");

Router::run('/');