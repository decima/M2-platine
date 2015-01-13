<?php

define("CURRENT_VERSION", 0.3);
ini_set("display_errors", FALSE);
if (!file_exists("config/settings.php")) {
    header("Location: ./install");
}

require 'system/tools.php';
require 'system/hooks.php';

require 'config/settings.php';

require 'system_modules/ModuleManager/module.php';


_Security::enable_error_handling();

$mm = new ModuleManager();
$mm->init_system_module();