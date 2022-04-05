<?php
// sources: https://www.php.net/manual/en/, https://cs4640.cs.virginia.edu/, https://genius.com/api-clients

spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

session_start();

$action = "login";
if (isset($_GET["action"]))
    $action = $_GET["action"];

$controller = new MMController($action);
$controller->run();