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

    public function widget($item=array()){
        $item["hello"] = array("permissions" => "access content", "callback" => array("HelloWorld", "widgetHello"));
        return $item;
    }


    public static function widgetHello(){
        return Theme::tabling(array(array(1,2,3,4,5)),array(1,2,3,4,5));
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

    public function menu($item = array()) {
        $item["/hello"] = array("callback" => array("HelloWorld", "sayHelloToWorld"));
        $item["/hello/@"] = array("callback" => array("HelloWorld", "sayHelloTo"));
        $item["/hello/@/@"] = array("callback" => array("HelloWorld", "sayHelloTo"));

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
