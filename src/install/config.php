<?php
    $myfile = "";

    foreach($_POST['data'] as $k => $v){
        $k1 = strtoupper($k);
        foreach($v as $k2 => $v2){
            $k2 = strtoupper($k2);
            $myfile .= "define(\"CONFIG_".$k1."_".$k2."\", \"".$v2."\");\n";
        }
    }

    echo $myfile;

    if (isset($_POST['config_show_only']) && $_POST['config_show_only'] == "show") {
      //show only if folder is not writeable

    } else {
        //write config file if folder is writeable
        if(!file_put_contents("../config/settings.php", $myfile)){}

    }
