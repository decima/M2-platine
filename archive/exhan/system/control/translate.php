<?php
function t($string, $replacer = array()) {
    foreach($replacer as $k => $v) {
        
        $string = str_replace($k, $v, $string);
    }
    return $string;
}

