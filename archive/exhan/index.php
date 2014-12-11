<?php
$_SESSION['log'] = array();
$user = null;
$group = null;
//echo 'com.exhan'.' '.'30121991\n<br/>';
//echo 'com.exhan'.' '.'Wv8zurR4fFrpUmPh\n';
//require 'settings.php';
require 'system/require.php';
system_reset_variables();


system_init();
system_session_start();

system_module_load();
method_invoke_all("main");



system_module_action();
system_theme_action();
system_session_save();
