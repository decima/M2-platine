<?php

class UserObject extends DataObject {

    public function index() {
        return array("uid");
    }

    public function tableName() {
        return "user";
    }

    public function load($uid) {
        return parent::load(array("uid" => $uid));
    }

    public function load_by_email($email) {
        return parent::load(array("email" => $email));
    }

    public function load_by_email_and_password($email, $password) {
        return parent::load(array("email" => $email));
    }

    private function encrypt_password($password) {
        return md5($password . CONFIG_SITE_COOKIE);
    }

}
