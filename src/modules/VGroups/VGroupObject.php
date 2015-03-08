<?php

class VGroupObject extends DataObject {

    public static function schema(&$schema) {
        $schema["virtual_group"] = array(
            "vgid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "name" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            "group_status" => Database::FIELD_TYPE_INT
        );
        $schema["virtual_group_members"] = array(
            "vgid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
            "uid" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY,
            "role" => Database::FIELD_TYPE_INT
        );
    }

    public function index() {
        return array("vgid");
    }

    public function tableName() {
        return "virtual_group";
    }

}
