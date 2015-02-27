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

    public static function get_head() {
        return self::$head;
    }
    public static function get_menu() {
        return self::$menu;
    }
    public static function &get_body() {
        return self::$body;
    }
    public static function get_title() {
        return self::$title;
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

    public static function body($index_debut = null, $nb = 0) {
        $b = self::get_body();
        $s = sizeof($b);

        if(isset($index_debut)){
            if($nb != 0){
                $max = $index_debut+$nb-1;
            }
            else {
                $max = $s - $index_debut;
            }

            for ($index_debut ; $index_debut <= $max ; $index_debut++) {
                echo $b[$index_debut] . "\n";
            }
        }
        else {
            foreach (self::$body as $s) {
                echo $s . "\n";
            }
        }
    }

    public static function add_to_body($element, $title = null, $index = 0) {
        $output = "";
        if($title != null){
            $output = "<h1>".$title."</h1>";
        }
        $output .= $element;
        self::$body[] = $output;
    }

    public static function clean_body_part($index = 0) {
        if(isset($index) && sizeof(self::$body) > 0)
            self::$body[$index] = "";
    }

    public static function add_to_head($element) {
        self::$head[] = $element;
    }

    public static function linking($link, $value, $out = false, $attr = array()){
        $output = "<a href='".$link."'";
        if($out)
            $attr["target"] = "_blank";

        foreach ($attr as $k => $v){
            $output .= " $k='$v'";
        }
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

    public static function process_form_elements(FormElement $element){
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
