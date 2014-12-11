<?php
interface form_extension {

    public function form_types();
    public function form_ui($name, $element);
}

class form implements system_module, form_extension {

    public function form_types() {
        return array("input","password","text","submit","label");
    }

    public function form_ui($name, $element) {
        $obj = new stdClass();
        $obj->attr = array();
        $obj->attr["class"] = "";
        $obj->attr["name"] = $name;
        $obj->label = isset($element["label"]) ? $element["label"] : null;

        switch($element["type"]) {
            case "label":
                $obj->html_tag = "span";
                $obj->html_content =
                        isset($element['value']) ?
                        $element['value'] : "";
                $obj->open_tag = false;
                break;
            case "text":
                $obj->html_tag = "textarea";
                $obj->html_content =
                        isset($element['value']) ?
                        $element['value'] : "";
                $obj->open_tag = false;
                break;
            case "submit":
                $obj->html_tag = "button";
                $obj->open_tag = false;
                $obj->attr["type"] = "submit";
                $obj->html_content = $element["value"];
                break;
            case "password":
                $obj->html_tag = "input";
                $obj->open_tag = true;
                $obj->attr["type"] = "password";
                break;
            case "input":
                $obj->html_tag = "input";
                $obj->open_tag = true;
                $obj->attr["type"] = "text";
                break;
        }
        if(isset($element["value"])) {

            $obj->attr["value"] = $element["value"];
            $obj->html_content = $element["value"];
        }
        return $obj;
    }

    public static function form_generate_field($name, $field) {

        $modules = get_all_of_interface("form_extension");
        $form_input;
        foreach($modules as $module) {

            $array = method_invoke($module, "form_types");

            if(in_array($field["type"], $array)) {
                $form_input = $module;
                break;
            }
        }

        return method_invoke($form_input, "form_ui", $name, $field);
    }

    public function priority() {
        
    }

    public function system_init() {
        
    }

}