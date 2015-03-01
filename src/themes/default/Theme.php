<?php

class Theme extends Themed {

    const STRUCT_404 = "404";

    /* Flags */
    protected static $flag_process_form_elements_checkbox_list_open = false;
    protected static $flag_process_form_elements_radio_list_open = false;


    public function process_theme($structure = self::STRUCT_DEFAULT) {
        require_once './themes/default/templates/' . $structure . '.php';
    }

    public function process_403() {
        $this->set_title("Erreur 403");
        $this->add_to_body(file_get_contents("./themes/default/pages/403.php"));
        $this->process_theme(self::STRUCT_BLANK);
        /*
        $this->add_to_body(file_get_contents("./themes/default/pages/403.php"));
        $this->process_theme(self::STRUCT_BLANK);
        */
    }

    public function process_404() {

        $this->set_title("Erreur 404");
        $this->add_to_body(file_get_contents("./themes/default/pages/404.php"));
        $this->process_theme(self::STRUCT_BLANK);

        //$this->process_theme(self::STRUCT_404);
    }

    public static function showMenu(){
        require_once './themes/default/templates/menu.php';
    }

    public static function add_to_body($element, $title = null, $index = null) {
        $output = "";
        if($title != null){
            $output = "<div class=\"titre\">".$title."</div>";
        }
        $output .= $element;
        $b = &self::get_body();
        if(isset($index) && sizeof($b) > $index)
            $b[$index] .= $output;
        else
            $b[] = $output;
    }

    public static function tabling($rows, $headers = array(), $hcol = array()) {
        $output = "<div style='padding-left: 20px; padding-right: 20px; padding-bottom: 20px;'>";
        $output .= "<table class=\"tableau\">\n";
        if (count($headers) > 0) {
            $output .="<thead>\n<tr>\n";
            foreach ($headers as $h)
                $output .="<th>" . $h . "</th>\n";
            $output .="</tr>\n</thead>\n";
        }
        $output .="<tbody>\n";
        $variant1 = "variant1";
        $variant2 = "variant2";
        foreach ($rows as $k => $r) {
            $output .="<tr class='values ";
                if($k % 2 == 0)
                    $output .= $variant1;
                else
                    $output .= $variant2;
            $output .= "'>\n";
            $a = array_shift($hcol);
            if($a != null) {
                $output .= "<th>".$a."</th>\n";
            }
            foreach ($r as $r2)
                $output.="<td>$r2</td>\n";
            $output .="</tr>\n";
        }
        $output .="</tbody>\n";
        $output .= "</table>";
        $output .= "</div>";

        return $output;
    }


    public static function displayNotification(){
        $output = "";
        foreach(Notification::getStatusNotifications() as $n){
            $output .= "<table class=\"bandeau_info\">";
                $output .= "<tr class=\"bandeau_info_inside\">";
                    $output .= "<td>";
                    switch($n->type){
                        case Notification::STATUS_INFO:
                            $output .= "<i class=\"fa fa-info fa-lg bandeau_info_inside_td\"></i>";
                            break;
                        case Notification::STATUS_ERROR:
                            $output .= "<i class=\"fa fa-remove fa-lg bandeau_info_inside_td\"></i>";
                            break;
                        case Notification::STATUS_WARNING:
                            $output .= "<i class=\"fa fa-warning fa-lg bandeau_info_inside_td\"></i>";
                            break;
                        case Notification::STATUS_SUCCESS:
                            $output .= "<i class=\"fa fa-check fa-lg bandeau_info_inside_td\"></i>";
                            break;
                    }
                    $output .= "</td>";
                    $output .= "<td class=\"bandeau_info_inside_td\">";
                        $output .= $n->message;
                    $output .= "</td>";
                $output .= "</tr>";
            $output .= "</table>";
        }
        return $output;
    }



    public static function process_form_elements(FormElement $element){
        ($element->getLabel() != null AND $element->getLabel() != "") ? $isLabel = true : $isLabel = false;
        ($element->getId() != null AND $element->getId() != "") ? $label = $element->getId() : $label = $element->getName();
        $output = "";

        // On case les checkbox / radio dans une div chez nous :)
        if(isset($element->getAttributes()['type'])) {
            if(!self::$flag_process_form_elements_radio_list_open){
                if (!self::$flag_process_form_elements_checkbox_list_open AND $element->getAttributes()['type'] == 'checkbox') {
                    self::$flag_process_form_elements_checkbox_list_open = true;
                    $output .= "<div style=\"float:left;\">";
                } else if (self::$flag_process_form_elements_checkbox_list_open AND $element->getAttributes()['type'] != 'checkbox') {
                    self::$flag_process_form_elements_checkbox_list_open = false;
                    $output .= "</div><div style=\"clear:both;\"></div>";
                }
            }
            if(!self::$flag_process_form_elements_checkbox_list_open) {
                if (!self::$flag_process_form_elements_radio_list_open AND $element->getAttributes()['type'] == 'radio') {
                    self::$flag_process_form_elements_radio_list_open = true;
                    $output .= "<div style=\"float:left;\">";
                } else if (self::$flag_process_form_elements_radio_list_open AND $element->getAttributes()['type'] != 'radio') {
                    self::$flag_process_form_elements_radio_list_open = false;
                    $output .= "</div><div style=\"clear:both;\"></div>";
                }
            }
        }
        else if(self::$flag_process_form_elements_radio_list_open OR self::$flag_process_form_elements_checkbox_list_open){
            self::$flag_process_form_elements_checkbox_list_open = false;
            self::$flag_process_form_elements_radio_list_open = false;
            $output .= "</div><div style=\"clear:both;\"></div>";
        }

        // Element auto-fermable
        if($element -> is_closed()){
            if ($isLabel AND isset($element->getAttributes()['type']) AND ($element->getAttributes()['type'] != 'radio' AND $element->getAttributes()['type'] != 'checkbox')) {
                $output .= "<div class=\"formulaire_ligne\"><label for=\"".$label."\">".$element->getLabel()."</label>";
            }
            if (isset($element->getAttributes()['type']) AND ($element->getAttributes()['type'] == 'checkbox' OR $element->getAttributes()['type'] == 'radio')){
                $output .= "<div class=\"FormElementCheckboxRadio\">";
            }

            $output .= "<" . $element->getBalise();
            if($element->getId() != null AND $element->getId() != "")
                $output .= " id=\"" . $element->getId() . "\"";
            if($element->getName() != null AND $element->getName() != "")
                $output .= " name=\"" . $element->getName() . "\"";
            $output .= " value=\"" . $element->getValue() . "\"";
            foreach ($element->getAttributes() as $k => $v) {
                $output .= " $k=\"$v\"";
                if($k == 'type' AND $v == 'radio')
                    $output .= " class=\"radio\"";
            }
            $output .= "/>";
            if ($isLabel AND isset($element->getAttributes()['type']) AND $element->getAttributes()['type'] == 'checkbox') {
                $output .= "<label for=\"".$label."\"><span class=\"ui\"></span><span class=\"label\">" . $element->getLabel() . "</span></label>";
                $output .= "</div>";
            }
            else if ($isLabel AND isset($element->getAttributes()['type']) AND $element->getAttributes()['type'] == 'radio') {
                $output .= "<label for=\"".$label."\"><span class=\"ui\"></span><span class=\"label\">" . $element->getLabel() . "</span></label>";
                $output .= "</div>";
            }
            else if ($isLabel AND isset($element->getAttributes()['type']) AND ($element->getAttributes()['type'] != 'radio' AND $element->getAttributes()['type'] != 'checkbox')) {
                $output .= "</div>";
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
