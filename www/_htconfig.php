<?php

if (file_exists(".htconfigSystem.php") == 1 && file_exists(".htconfigSite.php") == 1) {
    include(".htconfigSystem.php");
    include(".htconfigSite.php");

    session_start();
    $mysql = new mysqli($db_host, $db_user, $db_pass, $db_name);
    // Check connection
    if ($mysql->connect_error) {
        die("Connection failed: " . $mysql->connect_error);
    }
    $mysql->set_charset("utf8");
} else {
    header("location: ./configureDatabase.php");
}

?>