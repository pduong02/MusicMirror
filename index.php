<?php
//some edits
// sources: https://www.php.net/manual/en/, https://cs4640.cs.virginia.edu/, https://genius.com/api-clients

spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

session_start(); 

$action = "login";
if (isset($_GET["action"]))
    $action = $_GET["action"];

// If the user's email is not set in the cookies, then it's not
// a valid session (they didn't get here from the login page),
// so we should send them over to log in first before doing
// anything else!
if (!isset($_SESSION["email"]) or !isset($_SESSION["name"]) ) {
    // they need to see the login
    $action = "login";
}


$controller = new MMController($action);
$controller->run();