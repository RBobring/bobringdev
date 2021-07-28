<?php
echo '<div style="width: 70%; margin: 100px auto; font-size: 2rem; color: darkred; text-align: center;">';
echo 'System kann nicht gebootet werden!<br><br>';
foreach ($code as $name) {
    echo $location." => ". $name . "<br>";
}
echo '</div><br><br>';
die("secure die");