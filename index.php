<?php
// sources: https://www.php.net/manual/en/, https://cs4640.cs.virginia.edu/, https://genius.com/api-clients

spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

header("Location: templates/home.php", true, 303);