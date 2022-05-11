<?php

session_start();
include realpath(__DIR__) . "/include.php";

$auth = new AuthSSO();

if (isset($_POST['url'])) $auth->setCurrentLink($_POST['url']);

if ($auth->ensureAuthorized()) {

    $_SESSION['app']['serialnumber'] = $_SESSION['app']['uid'];
    header("LOCATION: " . $_POST['url']);

}
