<?php

/**
 * @moduleName HelloWorld
 * 
 * 
 * */
class HelloWorld implements Module {

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "HelloWorld",
            "readablename" => "Hello World - Sample Module"
        );
    }

    public function widget($item = array()) {
        $item["hello"] = array("permissions" => "access content", "callback" => array("HelloWorld", "widgetHello"));
        return $item;
    }

    public static function widgetHello() {
        return Theme::tabling(array(array(1, 2, 3, 4, 5)), array(1, 2, 3, 4, 5));
    }

    public static function sayHelloToWorld() {
        Theme::add_to_body("hello the World!");
        $theme = new Theme();
        $theme->set_title("Hello world sample module");
        $theme->process_theme(Theme::STRUCT_DEFAULT);
        return;
    }

    public static function sayHelloTo($name, $surname = null) {
        Theme::add_to_body("HELLO $name, nice to meet you!");
        if ($surname != null) {
            Theme::add_to_body("May I call you $surname?");
        }
        $theme = new Theme();
        $theme->set_title("Hello World");
        $theme->process_theme(Theme::STRUCT_BLANK);
        return;
    }

    public static function forming() {
        $form = new Form("POST", Page::url("/forms"));
        $input = new InputElement("login", null, "Identifiant :", "Pierre");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));

        // Balise, Name, Label, Value
        $input = new FormElement("select", "age", null, "Age :", 10);
        $input->addElement(new FormElement("option", "", "", "0-10", 0));
        $input->addElement(new FormElement("option", "", "", "10-20", 10));
        $input->addElement(new FormElement("option", "", "", "20-30", 20));
        $input->addElement(new FormElement("option", "", "", "30-40", 30));
        $input->addElement(new FormElement("option", "", "", "40-50", 40));
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));

        $input = new InputElement("mabox1", null, "0-10", 0, "checkbox");
        $input->setAttribute("checked", "checked");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("mabox2", null, "10-20", 10, "checkbox");
        $input->setAttribute("checked", "checked");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("mabox3", null, "20-30", 20, "checkbox");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("mabox4", null, "30-40", 30, "checkbox");
        $input->setAttribute("checked", "checked");
        $input->setAttribute("disabled", "disabled");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("mabox5", null, "40-50", 40, "checkbox");
        $input->setAttribute("disabled", "disabled");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));

        $input = new InputElement("monradio1", "monradio", "0-10", 0, "radio");
        $input->setAttribute("checked", "checked");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("monradio2", "monradio", "10-20", 10, "radio");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("monradio3", "monradio", "20-30", 20, "radio");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("monradio4", "monradio", "30-40", 30, "radio");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));
        $input = new InputElement("monradio5", "monradio", "40-50", 40, "radio");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));

        $input = new FormElement("textarea", "textarea_bloc", "description", t("Description : "), "Test");
        $input -> setAttribute("row", 6);
        $input -> setAttribute("col", 18);
        $input -> addClasses("actualite_area_text");
        $form->addElement($input);
        $form->addElement(new ClosedElement("br"));


        $input = new InputElement("monbutton", "monbutton", null, "Test JS", "button");
        $input->setAttribute("onclick", "alert('Test JS OK');");
        $form->addElement($input);

        $theme = new Theme();
        $theme->process_form($form);
        $theme->process_theme();
    }

    public function menu($item = array()) {
        $item["/hello"] = array("callback" => array("HelloWorld", "sayHelloToWorld"));
        $item["/hello/@"] = array("callback" => array("HelloWorld", "sayHelloTo"));
        $item["/hello/@/@"] = array("callback" => array("HelloWorld", "sayHelloTo"));
        $item["/forms"] = array("callback" => array("HelloWorld", "forming"));

        return $item;
    }

    public function schema($schema = array()) {
        $schema["test1"] = array(
            "id" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "firstname" => Database::FIELD_TYPE_STRING,
            "lastname" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
        );


        $schema["test2"] = array(
            "id" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "firstname" => Database::FIELD_TYPE_STRING,
            "lastname" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
        );

        return $schema;
    }

}
