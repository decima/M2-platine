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
        $output = self::forming($form);
        self::add_to_body($output);
    }

    public static function forming(Form $form){
        $output = "";
        $output .= "<".$form -> getBalise();
        foreach($form->getAttributes() as $k=>$v){
            $output .= " $k=\"$v\"";
        }
        $output .=">";
        foreach($form->getElements() as $v){
            $output .= static::process_form_elements($v);
        }
        $output .="</".$form -> getBalise().">";
        return $output;
    }

    protected static function process_form_elements(FormElement $element){
        ($element->getLabel() != null AND $element->getLabel() != "") ? $isLabel = true : $isLabel = false;
        ($element->getId() != null AND $element->getId() != "") ? $label = $element->getId() : $label = $element->getName();
        $output = "";

        // Element auto-fermable
        if($element -> is_closed()){
            if ($isLabel AND ($element->getAttributes()['type'] != 'radio' AND $element->getAttributes()['type'] != 'checkbox')) {
                $output .= "<label for=\"".$label."\">".$element->getLabel()."</label>";
            }
            $output .= "<" . $element->getBalise();
            if($element->getId() != null AND $element->getId() != "")
                $output .= " id=\"" . $element->getId() . "\"";
            if($element->getName() != null AND $element->getName() != "")
                $output .= " name=\"" . $element->getName() . "\"";
            $output .= " value=\"" . $element->getValue() . "\"";
            foreach ($element->getAttributes() as $k => $v) {
                $output .= " $k=\"$v\"";
            }
            $output .= "/>";
            if ($isLabel AND ($element->getAttributes()['type'] == 'radio' OR $element->getAttributes()['type'] == 'checkbox')) {
                $output .= "<label for=\"".$label."\">" . $element->getLabel() . "</label>";
            }
        }
        else {
            if($isLabel AND (strtolower($element -> getBalise()) == "select" OR strtolower($element -> getBalise()) == "textarea")){
                $output .= "<label for=\"".$label."\">".$element -> getLabel()."</label>";
            }
            $output .= "<".$element -> getBalise();
            if($element->getId() != null AND $element->getId() != "")
                $output .= " id=\"" . $element->getId() . "\"";
            if($element->getName() != null AND $element->getName() != "")
                $output .= " name=\"" . $element->getName() . "\"";
            $output .= " value=\"".$element -> getValue()."\"";
            foreach($element->getAttributes() as $k=>$v){
                $output .= " $k=\"$v\"";
            }
            $output .=">";
            if(sizeof($element->getElements()) > 0){
                foreach($element->getElements() as $v){
                    $output .= self::process_form_elements($v);
                }
            }
            else if(strtolower($element -> getBalise()) != "textarea") {
                $output .= $element->getLabel();
            }
            else {
                $output .= $element->getValue();
            }
            $output .="</".$element -> getBalise().">";
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
