<?php

use kernel\controller\router;
Router::add('/login', function() {
    include('project/admin/php/views/login.php');
}, "both");

Router::add('/register', function() {
    include('project/admin/php/views/register.php');
}, "both");

Router::add('/profile', function() {
    include('project/admin/php/views/profile.php');
}, "both");

Router::run('/');