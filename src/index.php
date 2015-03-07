<?php

define("CURRENT_VERSION", 0.3);
session_start();
ini_set("display_errors", TRUE);
if (!file_exists("config/settings.php")) {
    header("Location: ./install");
}

require 'system/tools.php';
require 'system/hooks.php';
require 'system/DataObject.php';

require 'config/settings.php';
require 'system/Database.php';
require 'system/Themed.php';
require 'system/Translator.php';
require 'system/Form.php';

require 'system/Page.php';
require 'system/moduleManager.php';
require 'system/Notification.php';
require 'system/Widget/module.php';
require 'system/File/module.php';

require 'cache/classes.php';
require 'cache/theme.php';

_Security::enable_error_handling();
error_reporting(E_ALL ^ E_STRICT);

$mm = new ModuleManager();
$mm->init_system_module();
