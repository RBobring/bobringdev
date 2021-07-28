<?php
use kernel\controller\router;

Router::add('/vita', function() {
    include('website/bobring_dev/php/views/vita.php');
}, "both");

Router::run('/');