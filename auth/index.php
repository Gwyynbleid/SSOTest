<?php //Used to verify and process login
session_start();

include realpath(__DIR__) . "/include.php";

$auth = new AuthSSO();


if ($auth->verifyResponse($_GET)) {

    header("Location: " . filter_input(INPUT_GET, 'state', FILTER_SANITIZE_URL));
    exit();


}
