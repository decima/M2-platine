<?php

class GroupObject extends DataObject {

    protected $_members = array();

    public static function schema(&$schema) {
        $schema["group"] = array(
            "gid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "label" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
        );
        $schema["user_group"] = array(
            "gid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
            "uid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
        );
    }

    public function index() {
        return array("gid");
    }

    public function tableName() {
        return "group";
    }

    public function __set($param, $value) {

        if ($param == "label") {
            $group = new GroupObject();

            if ($group->load_by_label($value)) {
                throw new Exception_Database_Exists();
            }
        }
        parent::__set($param, $value);
    }

    public function load($id) {
        return parent::load(array("gid" => $id));
    }

    public function load_by_label($label) {
        return parent::load(array("label" => $label));
    }

    public function load_members() {
        $list = Database::getAll("SELECT uid from " . CONFIG_DB_PREFIX . "user_group" . " where gid=" . $this->gid);
        foreach ($list as $l) {
            $d = new UserObject();
            $d->load($l->uid);
            $this->_members[$l->uid] = $d;
        }
    }

    public function members() {
        return $this->_members;
    }

    public function remove_member($uid) {
        unset($this->_members[$uid]);
        Database::delete("user_group", array("gid" => $this->gid, "uid" => $uid));
    }
public function delete(){
    $this->remove_all_members();
    return parent::delete();
}
    public function remove_all_members() {
        $this->_members = array();
        Database::delete("user_group", array("gid" => $this->gid));
    }

    public function add_member($uid) {
        if (!isset($this->_members[$uid])) {
            Database::insert("user_group", array("gid" => $this->gid, "uid" => $uid));
            $d = new UserObject();
            $d->load($uid);
            $this->_members[$uid] = $d;
        }
    }

}
