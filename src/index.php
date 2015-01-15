<?php

define("CURRENT_VERSION", 0.3);
ini_set("display_errors", TRUE);
if (!file_exists("config/settings.php")) {
    header("Location: ./install");
}

require 'system/tools.php';
require 'system/hooks.php';
require 'cache.php';
require 'config/settings.php';
require 'system/Database.php';
require 'system/moduleManager.php';


_Security::enable_error_handling();

$mm = new ModuleManager();
$mm->init_system_module();
