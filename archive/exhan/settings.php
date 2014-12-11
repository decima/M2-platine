<?php

mb_internal_encoding('UTF-8');
header('Content-type: text/html; charset=utf-8');
ini_set('display_errors', true);


define('SERVER_ROOT', '/');
define('PHYSICAL_PATH', dirname(__FILE__) . SERVER_ROOT);



$path = "http://" . $_SERVER["SERVER_NAME"];
if ($_SERVER["SERVER_PORT"] != 80) {
    $path .=":" . $_SERVER["SERVER_PORT"];
}



define('NET_ROOT_PATH', "/nearme/");

define('NET_PATH', $path . NET_ROOT_PATH);

define('JS_SETTINGS', '
    {"SERVER_ROOT":"' . SERVER_ROOT . '",
     "PHYSICAL_PATH":"' . PHYSICAL_PATH . '",
     "NET_ROOT_PATH":"' . NET_ROOT_PATH . '",
          "NET_PATH":"' . NET_PATH . '"}');





/* echo FTP_LOGIN . " - " . FTP_PASSWORD . "<br/>";
echo BDD_LOGIN . " - " . BDD_PASSWORD . "<br/>";
*/
