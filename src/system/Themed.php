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
    public static function add_to_menu($element) {
        self::$menu[] = $element;
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


    public static function process_form(Form $form){
        $output = "";
        $output .= "<".$form -> getBalise();
        foreach($form->getAttributes() as $k=>$v){
            $output .= " $k=\"$v\"";
        }
        $output .=">";
        foreach($form->getElements() as $v){
            $output .= self::process_form_elements($v);
        }

        $output .="</".$form -> getBalise().">";

        self::add_to_body($output);
    }

    private static function process_form_elements(FormElement $element){
        $output = "";
        switch(strtolower($element -> getBalise())){
            case "input":
                $output .= "<".$element -> getBalise();
                $output .= " name=\"".$element -> getName()."\"";
                if($element -> getLabel() != null){
                    $output .= "  label=\"".$element -> getLabel()."\"";
                }
                $output .= " value=\"".$element -> getValue()."\"";
                foreach($element->getAttributes() as $k=>$v){
                    $output .= " $k=\"$v\"";
                }
                $output .="/>";
                break;
            case "select":
                $output .= "<".$element -> getBalise();
                $output .= " name=\"".$element -> getName()."\"";
                if($element -> getLabel() != null){
                    $output .= "  label=\"".$element -> getLabel()."\"";
                }
                $output .= " value=\"".$element -> getValue()."\"";
                foreach($element->getAttributes() as $k=>$v){
                    $output .= " $k=\"$v\"";
                }
                $output .=">";
                foreach($element->getElements() as $v){
                    $output .= self::process_form_elements($v);
                }

                $output .="</".$element -> getBalise().">";
                break;
            case "option":
                $output .= "<".$element -> getBalise();
                $output .= " name=\"".$element -> getName()."\"";
                $output .= " value=\"".$element -> getValue()."\"";
                foreach($element->getAttributes() as $k=>$v){
                    $output .= " $k=\"$v\"";
                }
                $output .=">";
                $output .= $element -> getLabel();
                $output .="</".$element -> getBalise().">";
                break;
            default :
                $output .= "<".$element -> getBalise();
                $output .= " name=\"".$element -> getName()."\"";
                if($element -> getLabel() != null){
                    $output .= "  label=\"".$element -> getLabel()."\"";
                }
                $output .= " value=\"".$element -> getValue()."\"";
                foreach($element->getAttributes() as $k=>$v){
                    $output .= " $k=\"$v\"";
                }
                $output .=">";
                foreach($element->getElements() as $v){
                    $output .= self::process_form_elements($v);
                }

                $output .="</".$element -> getBalise().">";
                break;
        }
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
