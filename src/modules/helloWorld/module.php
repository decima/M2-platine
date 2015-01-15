<?php

class HelloWorld implements Module {

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "HelloWorld",
            "readablename" => "Hello World - Sample Module"
        );
    }

    public static function sayHelloToWorld() {
        return "Hello the World!";
    }

    public static function sayHelloTo($name) {
        return "HELLO $name, nice to meet you!";
    }

    public function menu($item = array()) {
        $item["/hello/world"] = array("callback" => array("HelloWorld", "sayHelloToWorld"));
        $item["/hello/@"] = array("callback" => array("HelloWorld", "sayHelloTo"));
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
