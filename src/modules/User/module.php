<?php

/**
 * @moduleName User
 * 
 * 
 * */
require_once("UserObject.php");

class User implements Module {

    public function info() {
        return array(
            "name" => "User",
            "readablename" => "User Manager"
        );
    }

    public function install() {
        
       $ret = UserObject::create(CONFIG_ADMIN_LOGIN, CONFIG_ADMIN_PASSWORD, "Admin", "Administrator");
       $ret = $ret && UserObject::create(CONFIG_ADMIN_LOGIN, CONFIG_ADMIN_PASSWORD, "Admin", "Administrator");
       
       return $ret;
    }

    public function schema($schema = array()) {
        UserObject::schema($schema);
        return $schema;
    }

    public function create($email, $password, $firstname = "", $lastname = "") {
        $user = new UserObject();
        try {
            $user->email = $email;
            $user->password = $password;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
        } catch (Exception_Database_Exists $e) {
            return false;
        }
        return true;
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
