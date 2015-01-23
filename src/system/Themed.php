<?php

abstract class Themed {

    private static $head = array();
    private static $menu = array();
    private static $body = array();

    static $title = null;

    const STRUCT_DEFAULT = "default";
    const STRUCT_BLANK = "blank";
    const STRUCT_ADMIN = "admin";

    public abstract function process_theme($structure = self::STRUCT_DEFAULT);
    public abstract function process_404();
    public abstract function process_403();
    
    public static function head() {
        foreach (self::$head as $s) {
            echo $s . "\n";
        }
    }

    public static function set_title($title){
        self::$title =  $title;
    }

    public static function menu() {
        foreach (self::$menu as $m) {
            echo $m . "\n";
        }
    }

    public static function body() {
        foreach (self::$body as $s) {
            echo $s . "\n";
        }
    }

    public static function add_to_body($element) {
        self::$body[] = $element;
    }

    public static function add_to_head($element) {
        self::$head[] = $element;
    }

    public static function linking($link, $value, $out = false){
        $output = "<a href='".$link."'";
        if($out)
            $output .= " target='_blank'";
        $output .= ">".$value."</a>";
        return $output;
    }

    public static function listing($list = array()) {
        $output = "<ul>";
        foreach ($list as $k => $l) {
            $output .="<li>$l</li>";
        }
        $output .= "</ul>";
        return $output;
    }

    public static function tabling($rows, $headers = array(), $hcol = array()) {
        $output = "<table>\n";
        if (count($headers) > 0) {
            $output .="<thead>\n<tr>\n";
            foreach ($headers as $h)
                $output .="<th>" . $h . "</th>\n";
            $output .="</tr>\n</thead>\n";
        }
        $output .="<tbody>\n";
        foreach ($rows as $k => $r) {
            $output .="<tr>\n";
            if($a = array_shift($hcol) != null) {
                $output .= "<th>".$hcol."</th>\n";
            }
            foreach ($r as $r2)
                $output.="<td>$r2</td>\n";
            $output .="</tr>\n";
        }
        $output .="</tbody>\n";
        $output .= "</table>";

        return $output;
    }

}

class View implements SystemModule {

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "View",
            "readablename" => "View Manager"
        );
    }

    public function priority() {
        return -96;
    }

    public function system_init() {
        
    }

}
