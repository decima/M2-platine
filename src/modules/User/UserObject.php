<?php

class UserObject extends DataObject {
   
    public static function schema(&$schema) {
        $schema["user"] = array(
            "uid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "email" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "password" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "firstname" => Database::FIELD_TYPE_STRING,
            "lastname" => Database::FIELD_TYPE_STRING
        );

        $schema["user_avatar"] = array(
            "uid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
            "fid" => Database::FIELD_TYPE_INT + Database::NOTNULLVAL
        );
    }

    public function __set($param, $value) {
        if ($param == "password") {
            $value = self::encrypt_password($value);
        }
        if ($param == "email") {
            $user = new UserObject();
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new Exception_Database_Format();
            }
            if($user->load_by_email($value)) {
                throw new Exception_Database_Exists();
            }
        }
        parent::__set($param, $value);
    }

    public function index() {
        return array("uid");
    }

    public function user_is_logged() {
        return isset($_SESSION['logged'])?$_SESSION['logged']:null;
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
        return parent::load(array("email" => $email, "password" => self::encrypt_password($password)));
    }

    public static function loadAllUsersWithout($array_id_users = array()) {
        $d = new UserObject();
        $request = "SELECT * FROM " . CONFIG_DB_PREFIX . $d->tableName();
        if(sizeof($array_id_users) > 0) {
            $request .= " WHERE uid NOT IN(".implode(",", $array_id_users).")";
        }
        $request .= " ORDER BY lastname ASC, firstname ASC";
        $results = Database::getAll($request);
        $list_of_users=array();
        if(is_array($results)){
            foreach($results as $r){
                if($r->uid != User::get_user_logged_id())
                    $list_of_users[] = $r->uid;
            }
        }
        return $list_of_users;
    }

    private static function encrypt_password($password) {
        return md5($password . CONFIG_SITE_COOKIE);
    }

    public function get_avatar(){
        if(!($fid = Database::getValue("SELECT fid FROM " . CONFIG_DB_PREFIX . "user_avatar WHERE uid = $this->uid"))){
            return false;
        } else {
            return Page::url("/file/$fid");
        }
    }

    public function set_avatar($fid){
        Database::insert("user_avatar", array("uid" => $this->uid, "fid" => $fid), true);
    }
}
