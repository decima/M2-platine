<?php

class Translator implements SystemModule {

    public function info() {
        return array("name" => "Translator", "readablename" => "Translations");
    }

    public function priority() {
        return 100;
    }

    public function system_init() {
        
    }

}

function t($string, $parameters = array()) {
    $res = $string;
    foreach ($parameters as $k => $v) {
        $res = str_replace($k, $v, $res);
    }
    return $res;
}
