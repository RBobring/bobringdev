<!DOCTYPE html>
<html lang="de" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="<?php echo $system::$head['viewport']; ?>" />
    <meta name="author" content="<?php echo $system::$head['author']; ?>" />
    <meta name="format-detection" content="<?php echo $system::$head['format-detection']; ?>" />
    <meta name="keywords" lang="de" content="<?php echo $system::$head['keywords']; ?>" />
    <meta name="description" content="<?php echo $system::$head['description']; ?>" />
    <meta name="color-scheme" content="dark light">
    <link rel="shortcut icon" href="<?php echo $system::$head['shortcut icon']; ?>" />
    <link rel="stylesheet" type="text/css" href="/kernel/libs/css/mobile.css?version=0.1" media="all" />
    <link rel="stylesheet" type="text/css" href="/kernel/libs/css/desktop.css?version=0.1" media="only screen and (min-width: 415px)">
    <script type="text/javascript" src="/kernel/libs/base.js?version=0.1"></script>
    <script type="text/javascript" src="/website/bobring_dev/html/js/script.js?version=0.1"></script>
    <?php
        $system->loadHead();
    ?>
    <title><?php echo $system::$head['title']; ?></title>
</head>