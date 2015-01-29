<?php

class UserObject extends DataObject {
    public static function schema(&$schema){
        $schema["user"] = array(
            "uid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "email" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "password" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "firstname" => Database::FIELD_TYPE_STRING,
            "lastname" => Database::FIELD_TYPE_STRING
        );
    }
    public function index() {
        return array("uid");
    }

    public function tableName() {
        return "user";
    }

    public function load($uid=array()) {
        return parent::load(array("uid" => $uid));
    }

    public function load_by_email($email) {
        return parent::load(array("email" => $email));
    }

    public function load_by_email_and_password($email, $password) {
        return parent::load(array("email" => $email,"password"=>$password));
    }

    private function encrypt_password($password) {
        return md5($password . CONFIG_SITE_COOKIE);
    }

}
