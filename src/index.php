<?php

define("CURRENT_VERSION", 0.3);
ini_set("display_errors", FALSE);
if (!file_exists("config/settings.php")) {
    header("Location: ./install");
}

require 'system/tools.php';
require 'config/settings.php';

_Security::enable_error_handling();

function definition() {
    _Security::deprecated();
    _Security::version(0, 0.1);
}

function oti($a, $b) {
    definition($a, $b);
}

function oh() {
    oti(new stdClass(), 22);
}

oh();
