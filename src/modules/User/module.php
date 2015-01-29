<?php

/**
 * @moduleName User
 * 
 * 
 **/
require_once("./UserObject.php");

class User implements Module {

    public function info() {
        return array(
            "name" => "User",
            "readablename" => "User Manager"
        );
    }

    public function install() {
        
    }

    public function schema($schema = array()) {
        $schema["user"] = array(
            "uid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "email" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "password" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "firstname" => Database::FIELD_TYPE_STRING,
            "lastname" => Database::FIELD_TYPE_STRING
        );
        return $schema;
    }

    public function create($email, $password, $firstname, $lastname) {
        Database::execute($sql);
    }

    public function menu($item = array()) {
        $item['/profile'] = array(
            "callback" => array("User", "page_profile")
        );
        return $item;
    }

    public static function page_login() {
        
    }

    public static function page_profile() {
        $theme = new Theme();
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

}
