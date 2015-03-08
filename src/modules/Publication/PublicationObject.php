<?php

class PublicationObject extends DataObject {

    public static function schema($items = array()) {
        $items['publications'] = array(
            "pid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "author" => Database::FIELD_TYPE_INT + Database::NOTNULLVAL,
            "date_published" => Database::FIELD_TYPE_INT + Database::NOTNULLVAL,
            "qrender" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "destination" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "content" => Database::FIELD_TYPE_TEXT + Database::NOTNULLVAL
        );
        return $items;
    }

    public static function loadAllByFriendship($a = array(), $page = 0, $limit = 100) {
        $friends = implode(" , ", $a);
        $page *=$limit;
        return Database::getAll("SELECT * FROM " . CONFIG_DB_PREFIX . "publications WHERE author IN ($friends) and destination='globale' order by date_published desc limit $page,$limit");
    }

    public function __construct() {
        parent::__construct();
        $this->date_published = time();
        $this->author = User::get_user_logged_id();
        $this->qrender = "Text";
        $this->destination = "globale";
    }

    public function __set($param, $val) {
        if ($param == "content") {
            $val = json_encode($val);
        }
        parent::__set($param, $val);
    }

    public function index() {
        return array("pid");
    }

    public function tableName() {
        return "publications";
    }

    public function load_by_author($author) {
        $cname = get_called_class();
        $o = new $cname();
        return Database::getAll("SELECT * FROM " . CONFIG_DB_PREFIX . $o->tableName() . " WHERE 1");
    }

    public function load($pid) {
        return parent::load(array("pid" => $pid));
    }

}
