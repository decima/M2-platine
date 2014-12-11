<?php
function str_compare_count_percent_char($str1, $str2) {
    $a = substr_count($str1, "%");
    $b = substr_count($str2, "%");
    return $a - $b;
}

function printer($var, $output = false) {
    $b = "<pre>".print_r($var, true)."</pre>";
    if($output) echo $b;
    return $b;
}

function stripslashes_deep($value) {
    if(is_array($value)) {
        $value = array_map('stripslashes_deep', $value);
    }elseif(is_object($value)) {
        $vars = get_object_vars($value);
        foreach($vars as $key => $data) {
            $value->{$key} = stripslashes_deep($data);
        }
    }elseif(is_string($value)) {
        $value = stripslashes($value);
    }

    return $value;
}

function print_date($time) {
    return date('l jS \of F Y h:i:s A', $time);
}

function secure_string($string, $replacer = array()) {
    foreach($replacer as $k => $v)
            $string = str_replace(
                $k, addslashes($v), $string
        );
    return $string;
}

function access_granted() {
    return true;
}

function callable_exists($string) {
    $array = explode("::", $string);
    if(count($array) > 1) {
        return method_exists($array[0], $array[1]);
    }else {
        return function_exists($array[0]);
    }
}